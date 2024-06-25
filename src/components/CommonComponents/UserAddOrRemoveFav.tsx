'use client'

import React, { useState, useEffect, useRef } from 'react'
// import { Link, useHistory } from 'react-router-dom'
// import axios from 'axios'
import axios from '@/lib/axios'
import { Container, Row, Button, Col, Modal, Form, Alert, Card } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import Link from 'next/link'
import {useAppContext} from '@/context'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function UserAddOrRemoveFav({ userId, checkUser, FavouriteUsers}) {
    const {AuthToken} = useAppContext()
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [isSuccess, setIsSuccess] = useState(null);

    useEffect(() => {
        // console.log('From Fav users use : ', userId);

    }, [FavouriteUsers])

    // Remove from Favourite
    function handleRemoveUserFromFav(favUserId) {
        const formdata = {
            'uid':checkUser.id,
            'fav_uid':favUserId,
        };
        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`}
        };
        axios.post(getPublicUrl+'/api/add-to-fav', formdata, config).then(response => {
            if( response.data.status == 200 ) {
                // setUserFavouritesData(null);
                setIsSuccess(true);
                setSuccessMessage(response.data.results.message);
                setTimeout(function () {
                    // fetchData()
                    // document.getElementById(`userCurrency-${userId}`).classList.remove("show", "d-block");
                    // document.querySelectorAll(".modal-backdrop").forEach(el => el.classList.remove("modal-backdrop"));
                    setSuccessMessage(null)
                }, 2000);
            } else if( response.data.errors !== '' ){
                setIsSuccess(false);
                setErrors(response.data.errors);
            }
        }).catch(error => {
            console.log('Form data : ', error);
            // setIsLoading(false);
            if (error.response && error.response.status === 422) {
                // Handle validation errors specifically
                setErrors(error.response.data.errors); // Set errors state
            } else {
                setErrors(null);
                // Handle other errors (e.g., network errors)
            }
        })
    }
    return(
        <>
            {checkUser && checkUser.id === userId ? (<>
                <li>
                    <img src={`${getPublicUrl}/images/filled-heart.svg`} alt={`favourite-users-Test`} />
                </li>
            </>) : (<>
                {FavouriteUsers ? (<>
                    <li onClick={(e) => handleRemoveUserFromFav(FavouriteUsers.fav_user_id)}>
                        <img src={`${getPublicUrl}/images/filled-heart.svg`} alt={`favourited-users-Test`} />
                    </li>
                </>) : (<>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="40" viewBox="0 0 45 40" fill="none">
                            <path d="M22.5 39.5L41.599 20.3617C43.792 18.1642 45 15.2422 45 12.1349C45 9.02699 43.792 6.1055 41.599 3.90799C39.406 1.71048 36.4905 0.5 33.389 0.5C30.2875 0.5 27.372 1.71048 25.179 3.90799L22.5 6.5925L19.821 3.90799C17.628 1.71048 14.712 0.5 11.611 0.5C8.50949 0.5 5.594 1.71048 3.401 3.90799C1.2075 6.1055 0 9.02699 0 12.1349C0 15.2427 1.2075 18.1642 3.401 20.3617L22.5 39.5ZM33.389 2.50411C35.956 2.50411 38.3695 3.50617 40.185 5.3249C42.0005 7.14363 43 9.56259 43 12.1349C43 14.7071 42 17.1256 40.185 18.9448L22.5 36.6662L4.815 18.9448C2.9995 17.1256 2 14.7071 2 12.1349C2 9.56259 2.9995 7.14413 4.815 5.3249C6.6305 3.50617 9.0435 2.50411 11.611 2.50411C14.178 2.50411 16.5915 3.50617 18.407 5.3249L22.5 9.42681L26.5935 5.3249C28.4085 3.50617 30.822 2.50411 33.389 2.50411Z" fill="#873D8F"/>
                        </svg>
                    </li>
                </>)}
            </>)}
        </>
    );
}