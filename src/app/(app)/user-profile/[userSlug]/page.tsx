import { Metadata } from 'next';
import React, { useState, useEffect } from 'react';
import axios from 'axios'
import UserDetails from './UserDetails'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL'];
const liveUrl = 'https://helpii.se'

const fetchUser = async (slug: string) => {
    const data = {'slug':slug}
    const config = { headers: { 'content-type': 'application/json' } };

    const userdatas = await fetch(`${getPublicUrl}/api/get-user-details/${slug}`, {
        method : "POST",
    });
    return userdatas.json();
}

// export async function generateMetadata({ params }: any) {
//     const userdatas = await fetchUser(params.userSlug);

//     // console.log('New Data From getMeta : ', userdatas);
//     return {
//         title : `${userdatas.alluserData.full_name} | Helpii`,
//         description : userdatas.alluserData.profile.about !=  null ? userdatas.alluserData.profile.about : '',
//         openGraph: {
//             title: userdatas.alluserData.full_name,
//             description : userdatas.alluserData.profile.about !=  null ? userdatas.alluserData.profile.about : '',
//             url : `${liveUrl}/api/get-user-details/${params.userSlug}`,
//             siteName: "Helpii",
//             images: [
//                 {
//                     url : `${liveUrl}/storage/${userdatas.alluserData.avatar_location}`
//                 }
//             ]
//         }
//     }
// }

export default function UserProfile( {params}: any ) {
    return (
        <UserDetails params={params.userSlug}/>
    );
};