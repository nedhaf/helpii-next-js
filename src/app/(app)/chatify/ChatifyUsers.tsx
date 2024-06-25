"use client"
import React, {useState, useEffect, useRef} from 'react';
import {useAppContext} from '@/context'
import axios from 'axios';
import { FaInbox, FaSmile, FaPaperPlane, FaArrowAltCircleLeft, FaStar, FaHome, FaInfo, FaFile} from "react-icons/fa";
import { formatDistanceToNow } from 'date-fns';
import { Link } from 'next/link';

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL'];
const getFrontPublicUrl = process.env['PUBLIC_FRONTEND_URL'];
const liveUrl = 'https://helpii.se'

export default function ChatifyUsers({channel,onSelectUser}){
    const {Authuser,AuthToken,ChatifyCounter,setChatifyCounter} = useAppContext()
    const [users, setUsers] = useState([])
    const [chat, setChat] = useState([])
    const [selectedChatUser, setSelectedChatUser] = useState(null)
    const [activeUser, setActiveUser] = useState(null)
    const [errors, setErrors] = useState(null)
    const [searchValue, setSearchValue] = useState('')
    const [suggestions, setSuggestions] = useState([])
    const [favoriteUsers, setFavoriteUsers] = useState([])


    useEffect(()=>{
        if (!Authuser) return;
        fetchUsers();
        getFavorites();

        if(channel){
            /*console.log("channel listen",channel);*/
            channel.listen('MessageReceive',(event) => {
                console.log('MessageReceive chatify User');
                fetchUsers();
                getTotalUnseenMessages();
            });

            channel.listen('ReceivedMessageSeen',(event) => {
                console.log('ReceivedMessageSeen chatify User');
                fetchUsers();
                getTotalUnseenMessages();
            });
        }

    },[Authuser,channel]);

    const handleInputChange = (event) => {
        const { value } = event.target;
        console.log("Search Value",value);
        if(value){
            console.log("Search Value",value);
            setSearchValue(value);
            searchUsers(value);
        }else{
            setSearchValue(value);
            setSuggestions([]);
        }
    };
    const handleSuggestionClick = (suggestion) => {
        setSearchValue(suggestion);
        setSuggestions([]);
    };
    const handleUserClick = (user) => {
        console.log("handleUserClick",user);
        onSelectUser(user); // Props pass through component parent component
        setActiveUser(user);
        messageMakeSeen(user.id);
        // call makeSeen
        window.history.pushState(null, '', `/chatify/${user.id}`); // Change URL without reloading
    };
    async function messageMakeSeen(user_id){
        try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                console.log('User data not available');
                return;
            }
            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            const formdata = {
                id:user_id
            };
            await axios.post(getPublicUrl+'/api/chat/makeSeen', formdata, config).then(response => {
                console.log("ChatifyUsers(makeSeen): response",response.data);
                setSuggestions(response.data);
            }).catch(error => {
                if (error.response && error.response.data && error.response.data.errors) {
                    if( error.response.data.errors.uid ) {
                        setErrors(error.response.data.errors.uid[0]);
                    } else if( error.response.data.errors.ub_id ) {
                        setErrors(error.response.data.errors.ub_id[0]);
                    }
                } else {
                    setErrors('An error occurred. Please try again.');
                }
                setTimeout(function () {
                    setErrors(null)
                }, 1500);
            })
        }catch(error){
            console.error('Error fetching users:',error);
        }
    };
    async function searchUsers(key){
        try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                console.log('User data not available');
                return;
            }
            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            await axios.get(getPublicUrl+'/api/chat/search?input='+key, config).then(response => {
                console.log("ChatifyUsers(search): response",response.data.records);
                setSuggestions(response.data.records);
            }).catch(error => {
                if (error.response && error.response.data && error.response.data.errors) {
                    if( error.response.data.errors.uid ) {
                        setErrors(error.response.data.errors.uid[0]);
                    } else if( error.response.data.errors.ub_id ) {
                        setErrors(error.response.data.errors.ub_id[0]);
                    }
                } else {
                    setErrors('An error occurred. Please try again.');
                }
                setTimeout(function () {
                    setErrors(null)
                }, 1500);
            })
        }catch(error){
            console.error('Error fetching users:',error);
        }
    };
    async function getTotalUnseenMessages(e){
        try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                console.log('User data not available');
                return;
            }else{
                console.log('User data:',Authuser.user.id);
            }
            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            await axios.get(getPublicUrl+'/api/chat/chatifyUnreadMsgCounter', config).then(response => {
                console.log("ChatifyUsers(chatifyUnreadMsgCounter): response",response.data.total);
                setChatifyCounter(response.data.total);
            }).catch(error => {
                if (error.response && error.response.data && error.response.data.errors) {
                    if( error.response.data.errors.uid ) {
                        setErrors(error.response.data.errors.uid[0]);
                    } else if( error.response.data.errors.ub_id ) {
                        setErrors(error.response.data.errors.ub_id[0]);
                    }
                } else {
                    //setErrors('An error occurred. Please try again.');
                }

                setTimeout(function () {
                    setErrors(null)
                }, 1500);
            })
            //setUsers(response.data);
        }catch(error){
            console.error('Error fetching users:',error);
        }
    };

    async function fetchUsers(e){
        try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                console.log('User data not available');
                return;
            }else{
                console.log('User data:',Authuser.user.id);
            }
            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            await axios.get(getPublicUrl+'/api/chat/get-Contacts', config).then(response => {
                console.log("ChatifyUsers(fetchUsers): response",response.data.contacts);
                setUsers(response.data.contacts);
            }).catch(error => {
                if (error.response && error.response.data && error.response.data.errors) {
                    if( error.response.data.errors.uid ) {
                        setErrors(error.response.data.errors.uid[0]);
                    } else if( error.response.data.errors.ub_id ) {
                        setErrors(error.response.data.errors.ub_id[0]);
                    }
                } else {
                    //setErrors('An error occurred. Please try again.');
                }

                setTimeout(function () {
                    setErrors(null)
                }, 1500);
            })
            //setUsers(response.data);
        }catch(error){
            console.error('Error fetching users:',error);
        }
    };
    async function getFavorites(e){
        try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                console.log('User data not available');
                return;
            }else{
                console.log('User data:',Authuser.user.id);
            }
            const formdata = {
                'id' : Authuser.user.id,
            };
            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            await axios.post(getPublicUrl+'/api/chat/favorites',formdata, config).then(response => {
                console.log("ChatifyUsers(favorites): response",response.data.favorites);
                if(response.data.favorites){
                    setFavoriteUsers(response.data.favorites);
                }else{
                    setFavoriteUsers([]);
                }

            }).catch(error => {
                if (error.response && error.response.data && error.response.data.errors) {
                    if( error.response.data.errors.uid ) {
                        setErrors(error.response.data.errors.uid[0]);
                    } else if( error.response.data.errors.ub_id ) {
                        setErrors(error.response.data.errors.ub_id[0]);
                    }
                } else {
                    setErrors('An error occurred. Please try again.');
                }

                setTimeout(function () {
                    setErrors(null)
                }, 1500);
            })
        }catch(error){
            console.error('Error fetching users:',error);
        }
    };

    const getTimeAgo = (createdAt) => {
        const parsedDate = new Date(createdAt);
        let timeAgo = formatDistanceToNow(parsedDate, { addSuffix: true });
        timeAgo = timeAgo.replace(' hours', 'h').replace(' minutes', 'm');
        timeAgo = timeAgo.replace(' hour', 'h').replace(' minute', 'm');
        timeAgo = timeAgo.replace(' less than a', '');
        return timeAgo.replace('about', '').trim();
    };

    return(
            <>
                <div className="messenger-listView">
                    <div className="m-header">
                        <nav>
                            <a href="#"><FaInbox /> <span className="messenger-headTitle">MESSAGES</span> </a>
                        </nav>
                        <input type="text" className="messenger-search" placeholder="Search" onChange={handleInputChange} />
                    </div>
                    <div className="m-body contacts-container">
                        <div className="show messenger-tab users-tab app-scroll" data-view="users" style={{ display: searchValue.length > 0 ? 'none' : 'block' }}>
                            <div className="favorites-section">
                                <p className="messenger-title"><span>Favorites</span></p>
                                <div className="messenger-favorites app-scroll-hidden">
                                    {favoriteUsers.length > 0 && (
                                        favoriteUsers.map((fuser) => (
                                            <div className="favorite-list-item">
                                                <a href={`/chatify/${fuser.user.id}`}>
                                                {fuser && fuser.user.avatar_location ? (
                                                    <div className="avatar av-s header-avatar" style={{ backgroundImage: `url("${getPublicUrl}/storage/${fuser.user.avatar_location}")` }}></div>
                                                ) : (
                                                    <div className="avatar av-s header-avatar" style={{ backgroundImage: `url("${getPublicUrl}/storage/avatars/Dummy_avtar.jpg")` }}></div>

                                                )}
                                                <p>
                                                    {`${fuser.user.first_name} ${fuser.user.last_name}`.length > 6 ?
                                                    `${`${fuser.user.first_name} ${fuser.user.last_name}`.substring(0, 6)}..` :
                                                    `${fuser.user.first_name} ${fuser.user.last_name}`}
                                                </p>
                                                </a>
                                            </div>
                                        ))
                                    )}
                                </div>
                            </div>
                            <p className="messenger-title"><span>All Messages</span></p>
                            <div className="listOfContacts" style={{width:"100%",height:"calc(100% - 272px)",position:"relative"}}>
                                    <table className="messenger-list-item" data-contact="fdf">
                                        <tbody>
                                            {users.length > 0 ? (
                                                users.map((user) => (
                                                    <tr key={user.id} className={`list-group-item ${activeUser && activeUser.id === user.id ? 'active' : ''}`} onClick={() => handleUserClick(user)}>
                                                        <td>
                                                            {user.avatar_location ? (
                                                                <img src={`${getPublicUrl}/storage/${user.avatar_location}`} alt="User Avatar" className="avatar" style={{ width: '50px', height: '50px' }} />
                                                            ) : (
                                                                <img src={`${getPublicUrl}/storage/avatars/Dummy_avtar.jpg`} alt="Default Avatar" className="avatar" style={{ width: '50px', height: '50px' }} />
                                                            )}
                                                        </td>
                                                        <td>
                                                            <p data-id={user.id} data-type="user">
                                                                {`${user.first_name} ${user.last_name}`}
                                                                <span className="contact-item-time" data-time={user.lastMessage.created_at}>
                                                                    { getTimeAgo(user.lastMessage.created_at) }
                                                                </span>
                                                            </p>
                                                            <span>
                                                                {user.id == user.lastMessage.to_id && (
                                                                    <span className="lastMessageIndicator">You :</span>
                                                                )}
                                                                {user.lastMessage.attachment ? (
                                                                    <>
                                                                    <FaFile />Attachment
                                                                    </>
                                                                ) : (
                                                                    <>
                                                                    {user.lastMessage.body}
                                                                    </>
                                                                )}
                                                                {user.unseenCounter > 0 && (
                                                                    <b>{user.unseenCounter}</b>
                                                                )}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                ))
                                            ) : (
                                                <tr><td className="list-group-item">No users available</td></tr>
                                            )}
                                        </tbody>
                                </table>
                            </div>
                        </div>
                        <div className="messenger-tab search-tab app-scroll" data-view="search" style={{ display: searchValue.length > 0 ? 'block' : 'none' }}>
                            <p className="messenger-title"><span>Search</span></p>
                            <div className="search-records">
                                {/*<p className="message-hint center-el"><span>Type to search..</span></p>*/}
                                <table>
                                    {suggestions.length > 0 ? (
                                        suggestions.map((user) => (
                                            <tr key={`suser_$user.id`}>
                                                <td>
                                                    <a href={`/chatify/${user.id}`}>
                                                    {user && user.avatar_location ? (
                                                        <div className="avatar av-m " style={{ backgroundImage: `url("${getPublicUrl}/storage/${user.avatar_location}")` }}></div>
                                                    ) : (
                                                        <div className="avatar av-m" style={{ backgroundImage: `url("${getPublicUrl}/storage/avatars/Dummy_avtar.jpg")` }}></div>

                                                    )}
                                                    </a>
                                                </td>
                                                <td>
                                                    {user ? (
                                                        <>
                                                            <p data-id={user.id} data-type="user">{`${user.first_name} ${user.last_name}`}</p>
                                                        </>
                                                    ) : (
                                                        <>
                                                            <p data-id={user.id} data-type="user">helpii</p>
                                                        </>
                                                    )}
                                                </td>
                                            </tr>
                                            ))
                                        ) : (
                                            <tr><td className="list-group-item"> No Users</td></tr>
                                        )}
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </>
        )
}
