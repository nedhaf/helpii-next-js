"use client"
import React, { useState, useEffect, useRef} from 'react'
import { Alert } from "react-bootstrap"
import {useAppContext} from '@/context'
import axios from 'axios';

import ImageComponent from './ImageComponent';

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL'];
const getFrontPublicUrl = process.env['PUBLIC_FRONTEND_URL'];
const liveUrl = 'https://helpii.se'

export default function ChatifyUserDetails({selectedUser,showUserDetails}){
    const {Authuser,AuthToken} = useAppContext();
    const [userDetails, setUserDetails] = useState(null);
    const [errors, setErrors] = useState(null);
    const [showUserInfo, setShowUserInfo] = useState(showUserDetails);
    const [sharePhotos, setSharePhotos] = useState('');
    const [lightboxOpen, setLightboxOpen] = useState(false);
    const userContainerRef = useRef(null);

    useEffect(() => {
        if (selectedUser) {
            console.log("selectedUser",selectedUser);
            setUserDetails(selectedUser);
            getSharedPhotos(selectedUser);
            userContainerRef.current?.scrollIntoView({ behavior: "smooth" });

        }
    }, [selectedUser]);

    useEffect(() => {
        if (selectedUser) {
            console.log("selectedUser",selectedUser);
            setUserDetails(selectedUser);
            getSharedPhotos(selectedUser);
        }
    }, [selectedUser]);

    useEffect(() => {
        setShowUserInfo(showUserDetails);
        console.log("showUserInfo",showUserInfo);
    }, [showUserDetails]);

    const handleDeleteConversation = (e) => {
        e.preventDefault();
        console.log("handleDeleteConversation");
        deleteConversation(selectedUser);
    };

    async function deleteConversation(selectedUser){
        try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                console.log('User data not available');
                return;
            }else{
                console.log('User data:',Authuser.user.id);
            }
            const formdata = {
                'id' : selectedUser.id,
            };
            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            await axios.post(getPublicUrl+'/api/chat/deleteConversation', formdata, config).then(response => {
                console.log("ChatifyMessage(deleteConversation): response",response.data);

            }).catch(error =>{
                if(error.response && error.response.data && error.response.data.errors){
                    if(error.response.data.errors.uid){
                        setErrors(error.response.data.errors.uid[0]);
                    }else if(error.response.data.errors.ub_id){
                        setErrors(error.response.data.errors.ub_id[0]);
                    }
                }else{
                    //setErrors('An error occurred. Please try again.');
                }

                setTimeout(function(){
                    setErrors(null)
                }, 1500);
            })
            //setUsers(response.data);
        }catch(error){
            console.error('Error in delete conversation:',error);
        }
    };

    async function getSharedPhotos(selectedUser){
        try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                console.log('User data not available');
                return;
            }else{
                console.log('User data:',Authuser.user.id);
            }
            const formdata = {
                'user_id' : selectedUser.id,
            };
            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            await axios.post(getPublicUrl+'/api/chat/shared', formdata, config).then(response => {
                console.log("ChatifyMessage(getSharedPhotos): response",response.data.shared);
                setSharePhotos(response.data.shared);
            }).catch(error =>{
                if(error.response && error.response.data && error.response.data.errors){
                    if(error.response.data.errors.uid){
                        setErrors(error.response.data.errors.uid[0]);
                    }else if(error.response.data.errors.ub_id){
                        setErrors(error.response.data.errors.ub_id[0]);
                    }
                }else{
                    //setErrors('An error occurred. Please try again.');
                }

                setTimeout(function(){
                    setErrors(null)
                }, 1500);
            })
            //setUsers(response.data);
        }catch(error){
            console.error('Error in getting sharedPhotos:',error);
        }
    };


    return(
            <>
                <div className={`messenger-infoView app-scroll ${!showUserInfo ? 'd-none' : ''}`}>
                    <nav>
                        <p>User Details</p>
                        <a href="#"><i className="fas fa-times"></i></a>
                        {errors && (
                            <Alert variant="danger" className="" dismissible>
                                <FaCircleXmark className="me-2" /> {errors}
                            </Alert>
                        )}
                    </nav>
                    {userDetails ? (
                        <>
                            {userDetails.avatar_location ? (
                                <div className="avatar av-l chatify-d-flex" style={{ backgroundImage: `url("${getPublicUrl}/storage/${userDetails.avatar_location}")` }}></div>
                            ) : (
                                <div className="avatar av-l chatify-d-flex" style={{ backgroundImage: `url("${getPublicUrl}/storage/avatars/Dummy_avtar.jpg")` }}></div>

                            )}
                            <p className="info-name">{`${userDetails.first_name} ${userDetails.last_name}`}</p>
                            <div className="user_email">
                                {`${userDetails.email}`}
                            </div>
                            <div className="messenger-infoView-btns" >
                                <a className="danger delete-conversation" onClick={handleDeleteConversation} style={{display: 'block'}}>Delete Conversation</a>
                            </div>
                            <div className="messenger-infoView-shared" style={{display: 'block'}}>
                                <p className="messenger-title"><span>Shared Photos</span></p>
                                <div className="shared-photos-list">
                                    {sharePhotos.length>0 && sharePhotos && sharePhotos.map((img, index) => (

                                         <ImageComponent key={index} img={img} />
                                    ))}
                                </div>
                            </div>
                        </>
                    ) : (
                        <p>No user selected</p>
                    )}
                    <div ref={userContainerRef}></div>
                </div>
            </>
        )
}