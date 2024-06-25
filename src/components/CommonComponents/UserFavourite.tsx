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

export default function UserFavourite({ userId, checkUser}) {
    const {AuthToken} = useAppContext()
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [isSuccess, setIsSuccess] = useState(null);
    const [userFavouritesData, setUserFavouritesData] = useState(null);


    useEffect(() => {
        // console.log('From Fav users use : ', userId);
        if( checkUser.id === userId ) {
            fetchData()
        }
    }, [])

    const fetchData = async () => {
        try {
            const data = {'uid':userId}
            const config = {
                headers: {
                    'content-type': 'application/json'
                }
            };
            await axios.post(getPublicUrl+'/api/get-fav-users', data, config).then(response => {
                if( response.data.status == 200 ) {
                    setUserFavouritesData(response.data.results)
                }else if( response.data.errors !== '' ){
                    setErrors(response.data.errors);
                }
            })
        } catch (error) {
            if (error.response && error.response.status === 422) {
                // Handle validation errors specifically
                setErrors(error.response.data.errors); // Set errors state
            } else {
                setErrors(null);
                // Handle other errors (e.g., network errors)
            }
        }
    };
    // Remove from Favourite
    function handleDeleteUserFromFav(favUserId) {
        const formdata = {
            'uid':userId,
            'fav_uid':favUserId,
        };
        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`}
        };
        axios.post(getPublicUrl+'/api/add-to-fav', formdata, config).then(response => {
            console.log('Favourited Users Response: ', response);
            if( response.data.status == 200 ) {
                setUserFavouritesData(null);
                setIsSuccess(true);
                setSuccessMessage(response.data.results.message);
                fetchData()
                setTimeout(function () {
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
        console.log('Remove from Fav : ', formdata);
    }

    // console.log('Favourites : ', userFavouritesData);
    return (
        <>
            <Row className="">
                {(successMessage || errors) && ( <div className={`col-md-12`}>
                    {successMessage && (
                        <Alert variant="success" className="" dismissible>
                            <FaCheck className="me-2" /> {successMessage}
                        </Alert>
                    )}
                    {errors && (
                        <Alert variant="danger" className="" dismissible>
                            <FaCircleXmark className="me-2" /> {errors}
                        </Alert>
                    )}
                </div>)}
                { userFavouritesData && userFavouritesData.length > 0 ? (
                    userFavouritesData.map((favourite, indx) => {
                        var price = '';
                        var time = '';
                        if( favourite.sp_skill_show_price == 'hour' || favourite.sp_skill_show_price == 'strings.new.hour' ) {
                            const perHourPrice = Math.round(favourite.sp_skill_price_per_hour) === favourite.sp_skill_price_per_hour ? favourite.sp_skill_price_per_hour : Math.round(favourite.sp_skill_price_per_hour);
                            price = perHourPrice;
                            time = 'Timme';
                        } else if( favourite.sp_skill_show_price == 'day' || favourite.sp_skill_show_price == 'strings.new.day' ) {
                            const perDayPrice = Math.round(favourite.sp_skill_price_per_day) === favourite.sp_skill_price_per_day ? favourite.sp_skill_price_per_day : Math.round(favourite.sp_skill_price_per_day);
                            price = perDayPrice;
                            time = 'Dag';
                        } else {
                            const perHourPrice = Math.round(favourite.sp_skill_price_per_hour) === favourite.sp_skill_price_per_hour ? favourite.sp_skill_price_per_hour : Math.round(favourite.sp_skill_price_per_hour);
                            const perDayPrice = Math.round(favourite.sp_skill_price_per_day) === favourite.sp_skill_price_per_day ? favourite.sp_skill_price_per_day : Math.round(favourite.sp_skill_price_per_day);
                            price = perHourPrice+' - '+perDayPrice;
                            time = 'Timme - Dag';
                        }
                        return (
                            <>
                                <Col lg={6} key={indx}>
                                    <Card className="favourites-card">
                                        <Row>
                                            <Col md={4}>
                                                <Card.Img className="favourites-profile-image" src={ favourite.sp_image ? `${getPublicUrl}${favourite.sp_image}` : `${getPublicUrl}/storage/avatars/dummy.png`  } alt="profile-image" />
                                            </Col>
                                            <Col md={8} className="skill-content">
                                                <Card.Body>
                                                        <div className={`favorite-card-remove-sec d-flex justify-content-end`} onClick={(e) => handleDeleteUserFromFav(favourite.user_id)}>
                                                            <img src={`${getPublicUrl}/images/remove-rectangle.png`} className={`remove-from-fav`} alt={`remove-from-favorite`}/>
                                                        </div>
                                                    <Card.Title>
                                                        <Link href={`/user-profile/${favourite.sp_slug}`}>{ favourite.sp_name }</Link>
                                                        {/*<img src={`${getPublicUrl}/images/skill-profile-icon-01.png`} alt="skill-profile-icon" />*/}
                                                    </Card.Title>
                                                    <div className={`favourites-inner-section-first`}>
                                                        { favourite.sp_skill_images && favourite.sp_skill_images.length > 0 ? (
                                                            <ul className="favourites-skillicon-list">
                                                                { favourite.sp_skill_images.map( ( favSkill, sIndx ) => {
                                                                    return(
                                                                        <>
                                                                            <li key={sIndx}><img src={`${getPublicUrl}${favSkill}`} alt="delivery-skill" /></li>
                                                                        </>
                                                                    );
                                                                } ) }
                                                            </ul>
                                                        ) : '' }
                                                        <div className="skill-rating mt-3 mb-3">
                                                            <img className="" src={`${getPublicUrl}/images/star-icon.png`} alt="star-icon" />
                                                            {favourite.rating ? favourite.rating : 0}
                                                        </div>
                                                    </div>
                                                    <div className="location mt-3">
                                                        <img src={`${getPublicUrl}/images/skill-location-icon.png`} alt="skill-location-icon" />
                                                        {favourite.address}
                                                    </div>

                                                    <div className="favourites-skill-price mt-5">{price} {favourite.currency}/{time}</div>
                                                </Card.Body>
                                            </Col>
                                        </Row>
                                    </Card>
                                </Col>
                            </>
                        )
                    })
                ) : <Col lg={12} className={`mt-5`}><div className="helpii-no-ads-skill-alert alert alert-secondary d-flex align-items-center" role="alert">
                        <img className={`helpii-no-ads-skill-img me-4`} src={`/no-ads.svg`} alt="no-ads-icon" />
                    <div className={`helpii-no-ads-skills-text`}>
                        Det finns inga favoritanvändare.
                    </div>
                </div></Col> }
            </Row>
        </>
    );
}
