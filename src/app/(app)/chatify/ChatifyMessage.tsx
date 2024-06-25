"use client"
import React, {useState,useEffect,useRef} from 'react';
import {useAppContext} from '@/context'
import axios from 'axios';
import { FaPlusCircle, FaSmile, FaPaperPlane, FaArrowAltCircleLeft, FaStar,FaRegStar, FaHome, FaInfo, FaTimesCircle, FaCheck, FaCheckDouble, FaTrash} from "react-icons/fa";
import Echo from 'laravel-echo';
import { formatDistanceToNow } from 'date-fns';
import { Modal, Button } from 'react-bootstrap';
import Picker from 'emoji-picker-react';
import { useAuth } from '@/hooks/auth'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL'];
const getFrontPublicUrl = process.env['PUBLIC_FRONTEND_URL'];
const liveUrl = 'https://helpii.se'

export default function ChatifyMessage({channel, selectedUser, onSelectUserInfo}){
    const {Authuser,AuthToken,ChatifyCounter,setChatifyCounter} = useAppContext();
    const [users, setUsers] = useState([]);
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState('');
    const [errors, setErrors] = useState(null);
    const [userDetails, setUserDetails] = useState(null);
    const containerRef = useRef(null);
    const [showUserInfo, setShowUserInfo] = useState(true);
    const [file, setFile] = useState(null);
    const [previewURL, setPreviewURL] = useState('');
    const [hoveredMessageId, setHoveredMessageId] = useState(null);
    const [showConfirmation, setShowConfirmation] = useState(false);
    const [messageIdToDelete, setMessageIdToDelete] = useState(null);
    const [userAddedToFavorite, setUserAddedToFavorite] = useState(false);
    const [showEmojiPicker, setShowEmojiPicker] = useState(false);


    useEffect(() => {
        if (selectedUser && Authuser){
            messageMakeSeen(selectedUser.id);
            fetchMessages(selectedUser);
            setUserDetails(selectedUser);
            fetchSelectedUserData();

            console.log("selectedUser112", selectedUser);

            console.log("channel listen",channel);

            channel.listen('MessageSent',(event) => {
            // channel.listen('messaging',(event) => {
                // messageMakeSeen(selectedUser.id);
                // fetchMessages(selectedUser);
                containerRef.current.scrollTop = 0;
                event.message.body = event.message.message;
                setMessages((prevItems) => [...prevItems, event.message]);
                console.log('Received new message:',event.message);

            });

            channel.listen('MessageSeen',(event) => {
                console.log('Received MessageSeen:');
                fetchMessages(selectedUser);
            });

            return () => {
                // Clean up function to stop listening when component unmounts
                channel.stopListening('MessageSent');
                // channel.stopListening('MessageSent');
            };

            console.log("chat_selectedUser",selectedUser.id);
            console.log("Authuser",Authuser.user.id);
            console.log("Authuser",Authuser.user.id);
        }
    }, [selectedUser,Authuser,channel]);

    useEffect(() => {
        containerRef.current?.scrollIntoView({ behavior: "smooth" });
    }, [messages]);

    useEffect(() => {
    }, [userAddedToFavorite]);

    const handleToggleEmojiPicker = () => {
        setShowEmojiPicker(prevState => !prevState);
    };

    const onEmojiClick = (event, emojiObject) => {
        console.log('emojiObject:', emojiObject.target);
        console.log('emojiObject:', event.emoji);
        if(event.emoji){
            setInput((prevInput) => prevInput + event.emoji);
        } else {
            // In case the structure is different, use a fallback
            console.warn('Invalid emojiObject structure:', emojiObject);
        }
        setShowEmojiPicker(false);
    };



    const onMessageViewed = (messageId) => {
        //console.log()
        // messageMakeSeen(selectedUser.id);
    };

    const handleReaction = (emoji) => {
        console.log('Selected Emoji:', emoji);
        // Handle the selected emoji
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

    async function sendMessage(){
        try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                //console.log('User data not available');
                return;
            }else{
                //console.log('User data:',Authuser.user.id);
            }
            // console.log("selectedUser=",selectedUser.id)
            // console.log("sendMessageData, file, input=",file,input)
            const formData = new FormData();
            formData.append('id', selectedUser.id);
            if(file){
                formData.append('file', file);
                setPreviewURL('');
            }
            // Append the file directly
            formData.append('message', input);
            formData.append('temporaryMsgId', 'temp_1');

            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            await axios.post(getPublicUrl+'/api/chat/sendMessage', formData, config).then(response => {
                response.data.message.body = response.data.message.message
                if(file){
                    response.data.message.attachment.new_name = response.data.message.attachment.file;
                }
                //console.log("ChatifyMessage(sendMessage): response",response.data.message);
                containerRef.current.scrollTop = 0;
                setMessages((prevItems) => [
                    ...prevItems,
                    response.data.message,
                ]);
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
            console.error('Error fetching users:',error);
        }
    };

    async function fetchMessages(selectedUser){
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
            await axios.post(getPublicUrl+'/api/chat/fetchMessages', formdata, config).then(response => {
                console.log("ChatifyMessage(fetchMessages): response",response.data.messages);
                setMessages(response.data.messages.reverse());
            }).catch(error =>{
                if(error.response && error.response.data && error.response.data.errors){
                    if(error.response.data.errors.uid){
                        setErrors(error.response.data.errors.uid[0]);
                    }else if(error.response.data.errors.ub_id){
                        setErrors(error.response.data.errors.ub_id[0]);
                    }
                }else{
                    setErrors('An error occurred. Please try again.');
                }
                setTimeout(function(){
                    setErrors(null)
                }, 1500);
            })
        }catch(error){
            console.error('Error fetching users:',error);
        }
    };

    async function userFavorite(){
        try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                console.log('User data not available');
                return;
            }
            const formdata = {
                'user_id' : selectedUser.id,
            };
            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            await axios.post(getPublicUrl+'/api/chat/star', formdata, config).then(response => {
                console.log("ChatifyMessage(star): response",response.data);
                if(response.data.status){
                    setUserAddedToFavorite(true);
                }else{
                    setUserAddedToFavorite(false);
                }
                window.location.reload();
            }).catch(error =>{
                if(error.response && error.response.data && error.response.data.errors){
                    if(error.response.data.errors.uid){
                        setErrors(error.response.data.errors.uid[0]);
                    }else if(error.response.data.errors.ub_id){
                        setErrors(error.response.data.errors.ub_id[0]);
                    }
                }else{
                    setErrors('An error occurred. Please try again.');
                }
                setTimeout(function(){
                    setErrors(null)
                }, 1500);
            })
        }catch(error){
            console.error('Error deleteMessage:',error);
        }
    };

    async function fetchSelectedUserData(){
        try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                console.log('User data not available');
                return;
            }
            const formdata = {
                'id' : selectedUser.id,
            };
            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            await axios.post(getPublicUrl+'/api/chat/idInfo', formdata, config).then(response => {
                console.log("ChatifyMessage(idInfo): response",response.data);
                setUserAddedToFavorite(response.data.favorite);
            }).catch(error =>{
                if(error.response && error.response.data && error.response.data.errors){
                    if(error.response.data.errors.uid){
                        setErrors(error.response.data.errors.uid[0]);
                    }else if(error.response.data.errors.ub_id){
                        setErrors(error.response.data.errors.ub_id[0]);
                    }
                }else{
                    setErrors('An error occurred. Please try again.');
                }
                setTimeout(function(){
                    setErrors(null)
                }, 1500);
            })
            //setUsers(response.data);
        }catch(error){
            console.error('Error deleteMessage:',error);
        }
    };

    async function deleteMessage(msg_id){
        try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                console.log('User data not available');
                return;
            }
            const formdata = {
                'id' : msg_id,
            };
            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            await axios.post(getPublicUrl+'/api/chat/deleteMessage', formdata, config).then(response => {
                console.log("ChatifyMessage(deleteMessage): response",response.data);
                setMessages(messages.filter(msg => msg.id !== msg_id));
            }).catch(error =>{
                if(error.response && error.response.data && error.response.data.errors){
                    if(error.response.data.errors.uid){
                        setErrors(error.response.data.errors.uid[0]);
                    }else if(error.response.data.errors.ub_id){
                        setErrors(error.response.data.errors.ub_id[0]);
                    }
                }else{
                    setErrors('An error occurred. Please try again.');
                }
                setTimeout(function(){
                    setErrors(null)
                }, 1500);
            })
            //setUsers(response.data);
        }catch(error){
            console.error('Error deleteMessage:',error);
        }
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
                console.log("ChatifyMessage(makeSeen): response",response.data);
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

    const getTimeAgo = (createdAt) => {
        const parsedDate = new Date(createdAt);
        let timeAgo = formatDistanceToNow(parsedDate, { addSuffix: true });
        timeAgo = timeAgo.replace(' hours', 'h').replace(' minutes', 'm');
        timeAgo = timeAgo.replace(' hour', 'h').replace(' minute', 'm');
        timeAgo = timeAgo.replace(' less than a', '');
        return timeAgo.replace('about', '').trim();
    };

    const handleShowUserDetailInfo = () => {
        setShowUserInfo(prevState => !prevState);
        onSelectUserInfo(!showUserInfo);
    };

    const handleFavorite = () => {
        userFavorite();
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (input || file) {
            sendMessage(input, file);
        }
        console.log("input",input)
        // Clear the input and file state after sending the message
        setInput('');
        setFile(null);
    };

    const handleFileChange = (e) => {
        const selectedFile = e.target.files[0];
        setFile(selectedFile);
        console.log("File=",selectedFile);

        const reader = new FileReader();
        reader.onloadend = () => {
            setPreviewURL(reader.result);
        };
        if (selectedFile) {
            reader.readAsDataURL(selectedFile);
        } else {
            setPreviewURL('');
        }
    };

    const handleMouseEnter = (messageId) => {
        setHoveredMessageId(messageId);
    };

    const handleMouseLeave = (messageId) => {
        setHoveredMessageId(messageId);
    };

    const handleDelete = (messageId) => {
        setShowConfirmation(true);
        setMessageIdToDelete(messageId);
    };

    const handleMsgDeleteConfirmation = (confirmed) => {
        if(confirmed){
            deleteMessage(messageIdToDelete);
        }
        setMessageIdToDelete(null);
        setShowConfirmation(false);
    };

    const handleDeleteImage = () => {
        setFile(null);
        setPreviewURL('');
        document.getElementById('file-input').value = '';
    };

    const handleInputChange = (e) => {
        setInput(e.target.value)
        //messageMakeSeen(selectedUser.id);
    };

    const handleInputClick = (e) => {
        setInput(e.target.value)
        messageMakeSeen(selectedUser.id);
    };

    return(
            <>
                <div className="messenger-messagingView">
                    <div className="m-header m-header-messaging">
                        <nav className="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                            <div className="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                                <a href="#" className="show-listView"><FaArrowAltCircleLeft /></a>
                                {userDetails && userDetails.avatar_location ? (
                                    <div className="avatar av-s header-avatar" style={{ backgroundImage: `url("${getPublicUrl}/storage/${userDetails.avatar_location}")` }}></div>
                                ) : (
                                    <div className="avatar av-s header-avatar" style={{ backgroundImage: `url("${getPublicUrl}/storage/avatars/Dummy_avtar.jpg")` }}></div>

                                )}
                                <a href="javascript:void(0)" className="user-name">&nbsp;
                                {userDetails ? (
                                    <>
                                        {`${userDetails.first_name} ${userDetails.last_name}`}
                                    </>
                                ) : (
                                    <>
                                        helpii
                                    </>
                                )}
                                </a>
                            </div>
                            <nav className="m-header-right">
                                {userDetails && (
                                    <a className={ userAddedToFavorite ? 'favorite-added' : 'add-to-favorite'} style={{display:'block'}} onClick={handleFavorite}><FaStar/></a>
                                )}
                                <a href="/"><FaHome/></a>
                                <a className="show-infoSide" onClick={handleShowUserDetailInfo}><FaInfo /></a>
                            </nav>
                        </nav>
                    </div>
                    <div className="m-body messages-container app-scroll">
                        <div className="messages" style={{height:"300px"}}>
                            {messages.length > 0 && messages.map((msg, index) => {
                                // Parse the attachment JSON string


                                // const attachment = msg.attachment ? JSON.parse(msg.attachment) : null;

                                const attachment = typeof msg.attachment === 'string' ? JSON.parse(msg.attachment) : msg.attachment;

                                const timeAgo = getTimeAgo(msg.created_at);

                                let attachmentType = 'unknown';
                                let attachmentFile;
                                let fileExtension;

                                if(attachment && attachment.new_name) {
                                    // Extract file extension from the old_name property
                                    fileExtension = attachment.new_name.split('.').pop().toLowerCase();
                                    attachmentFile =  getPublicUrl+'/storage/attachments/'+attachment.new_name;

                                    // Determine the attachment type based on file extension
                                    switch (fileExtension) {
                                        case 'jpg':
                                        case 'jpeg':
                                        case 'png':
                                        case 'gif':
                                            attachmentType = 'image';
                                            break;
                                        case 'zip':
                                            attachmentType = 'zip';
                                            break;
                                        case 'rar':
                                            attachmentType = 'rar';
                                            break;
                                        case 'txt':
                                            attachmentType = 'txt';
                                            break;
                                        default:
                                            attachmentType = 'unknown';
                                            break;
                                    }
                                }
                                return (
                                    <div key={index} className={msg.from_id === Authuser.user.id ? 'message-card mc-sender' : 'message-card sent-message'} onMouseEnter={() => handleMouseEnter(msg.id)} onMouseLeave={handleMouseLeave}>
                                        {hoveredMessageId === msg.id && msg.from_id === Authuser.user.id && (
                                            <div className="actions">
                                                <FaTrash
                                                    className="delete-icon"
                                                    onClick={() => handleDelete(msg.id)}
                                                />
                                            </div>
                                        )}
                                        <div className="message-card-content">
                                            {msg.body && (
                                                <div className="message" id={`message-${msg.id}`} data-message-id={msg.id}>
                                                    {msg.body}
                                                    <span data-time={msg.created_at} className="message-time">
                                                        {msg.from_id === Authuser.user.id && (
                                                            msg.seen ? (
                                                                <FaCheckDouble />
                                                            ) : (
                                                                <FaCheck />
                                                            )
                                                        )}
                                                        <span className="time">{timeAgo}</span>
                                                    </span>
                                                </div>
                                            )}
                                            {attachment && attachment.new_name && (
                                                <div className="image-wrapper" style={{textAlign: "end"}}>
                                                    {attachmentType === 'image' && (
                                                        <div className="image-file chat-image" style={{backgroundImage: `url(${attachmentFile})`}}>
                                                            <div>{attachment.new_name}</div>
                                                        </div>
                                                    )}
                                                    {attachmentType === 'zip' && (
                                                        <a href={attachmentFile} download>{attachment.new_name}</a>
                                                    )}
                                                    {attachmentType === 'rar' && (
                                                        <a href={attachmentFile} download>{attachment.new_name}</a>
                                                    )}
                                                    {attachmentType === 'txt' && (
                                                        <div className="text-file">{/* Render text file contents here */}</div>
                                                    )}
                                                    {attachmentType === 'unknown' && (
                                                        <div>Unknown attachment type</div>
                                                    )}
                                                    <div style={{marginBottom: "5px"}}>
                                                        <span data-time={msg.created_at} className="message-time" >
                                                            {msg.from_id !== Authuser.user.id && (
                                                                msg.seen ? (
                                                                    <FaCheckDouble />
                                                                ) : (
                                                                    <FaCheck />
                                                                )
                                                            )}
                                                            <span className="time">{timeAgo}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                );
                            })}
                            <div ref={containerRef}></div>
                        </div>
                    </div>
                    <div className="messenger-sendCard" style={{ display: 'block' }}>
                        {previewURL && (
                            <div className="image-preview d-block">
                                <img src={previewURL} alt="Preview" style={{ width: '300px' }}/>
                                <button onClick={handleDeleteImage} className="delete-image-button">
                                    <FaTimesCircle />
                                </button>
                            </div>
                        )}
                        {userDetails && (
                        <form id="message-form" encType="multipart/form-data" onSubmit={handleSubmit}>
                            <label>
                                <FaPlusCircle />
                                <input id="file-input" type="file" className="upload-attachment" name="file" accept=".png, .jpg, .jpeg, .gif, .zip, .rar, .txt" onChange={handleFileChange}/>
                            </label>
                            <button type="button" className="emoji-button" onClick={handleToggleEmojiPicker}>
                                <FaSmile />
                            </button>
                            {showEmojiPicker &&  <div style={{ width: '500px' }}><Picker skinTonesDisabled="true" showPreview="false"  onEmojiClick={onEmojiClick} pickerStyle={{ width: '100%', height: '100%' }}/></div> }
                            <textarea name="message" className="m-send app-scroll" placeholder="Type a message.." value={input} onChange={(e) => handleInputChange(e)} onClick={(e) => handleInputClick(e)}/>
                            <button type="submit" disabled={!input.trim() && !file} className="send-button">
                                <FaPaperPlane />
                            </button>
                        </form>
                        )}
                    </div>
                </div>
                <Modal show={showConfirmation} onHide={() => handleMsgDeleteConfirmation(false)}>
                    <Modal.Header closeButton>
                        <Modal.Title>Delete Message</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>Are you sure you want to delete this message?</Modal.Body>
                    <Modal.Footer>
                        <Button variant="secondary" onClick={() => handleMsgDeleteConfirmation(false)}>
                            Cancel
                        </Button>
                        <Button variant="danger" onClick={() => handleMsgDeleteConfirmation(true)}>
                            Delete
                        </Button>
                    </Modal.Footer>
                </Modal>
            </>
    );

}
