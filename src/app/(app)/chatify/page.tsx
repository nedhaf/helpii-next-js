"use client"
import { Metadata } from 'next';
import React, { useState, useEffect } from 'react';
import dynamic from 'next/dynamic';
import axios from 'axios';
import { loadEnvConfig } from '@next/env'
import '@/app/style/style.css'
import '@/app/style/bootstrap.css'
// import 'bootstrap/dist/css/bootstrap.css';
import '@/assets/chatify/style.css'
import '@/assets/chatify/light.mode.css'
import {useRouter, useParams, useSearchParams} from 'next/navigation'
import {useAppContext} from '@/context'
import ChatifyUsers from './ChatifyUsers'
import ChatifyMessage from './ChatifyMessage'
import ChatifyUserDetails from './ChatifyUserDetails'

import PusherSetup from './PusherSetup';


const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL'];
const getFrontPublicUrl = process.env['PUBLIC_FRONTEND_URL'];
const liveUrl = 'https://helpii.se'

export default function Chatify({ userId }) {
    const {Authuser,AuthToken} = useAppContext();
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState('');
    const [users, setUsers] = useState([]);
    const [errors, setErrors] = useState(null)
    const [selectedChatUser, setSelectedChatUser] = useState(null);
    const [channel, setChannel] = useState(null);
    const [showUserInfo, setShowUserInfo] = useState(true);

    useEffect(() => {
        if (!Authuser) return;
        console.log("Authuser=",Authuser);
        console.log("AuthToken=",AuthToken);

        if(AuthToken && Authuser){

            const echo = PusherSetup(AuthToken);
            const channel = echo.private(`Chat.User.${Authuser.user.id}`);

            // const clientSendChannel = echo.private(`${channelName}.${Authuser.user.id}`);
            // const clientListenChannel = echo.private(`${channelName}.${Authuser.user.id}`);

            /*clientListenChannel.listen('messaging', (data) => {
                console.log('Received new message:',data);
            });

              clientListenChannel.listen('messaging', (data) => {
                console.log('Received new message:',data);
            });
*/



            // const channel = `private-chatify.${Authuser.user.id}`;
            // const channel = echo.private(`private-chatify.${Authuser.user.id}`);

            setChannel(channel);
            // setChannel(clientListenChannel);

            return () => {
                channel.unsubscribe();
               // echo.leave(`${channelName}.${Authuser.user.id}`);

            };
        }

    }, [Authuser,AuthToken]);

    useEffect(() => {
        if (userId && Authuser){
            console.log("userId=====",userId)
            fetchUserById(userId);
        }
    },[userId,Authuser]);

    async function fetchUserById(userId){
        console.log("fetchUserById",userId)
         try {
            if(!Authuser || !Authuser.user || !Authuser.user.id){
                console.log('User data not available');
                return;
            }
            const formdata = {
                'id' : parseInt(userId)
            };
            const config = {
                headers: { Authorization: `Bearer ${AuthToken}`}
            };
            await axios.post(getPublicUrl+'/api/chat/idInfo', formdata, config).then(response => {
                console.log("page.tsx(fetchUserById): response",response.data.fetch);
                setSelectedChatUser(response.data.fetch);
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

    const handleUserSelect = (user) => {
        console.log("page.tsx(handleUserSelect)",user);
        setSelectedChatUser(user);
    };
    const handleShowUserDetailInfo = (val) => {
        console.log("handleShowUserDetailInfo:",val);
        setShowUserInfo(val);
    };
    return (
            <>
                <div className="container-fluid">
                    <div className="row">
                        <div className="messenger">
                            <ChatifyUsers onSelectUser={handleUserSelect} channel={channel} />
                            <ChatifyMessage selectedUser={selectedChatUser} channel={channel} onSelectUserInfo={handleShowUserDetailInfo}/>
                            <ChatifyUserDetails selectedUser={selectedChatUser} showUserDetails={showUserInfo}/>
                        </div>
                    </div>
                </div>
            </>
    )
}
