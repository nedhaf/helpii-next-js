"use client"

import React, { useState, useEffect, useRef } from 'react';
import { getStaticProps } from 'next';
// import axios from 'axios'
import axios from '@/lib/axios'
import {useAppContext} from '@/context'
import { Link } from 'react-router-dom';
import { useRouter, useParams, useSearchParams } from 'next/navigation'
import { Container, Row, Col, Modal, Form, Button, Card } from 'react-bootstrap';
import CreateSkill from "@/components/Modals/SkillModals/CreateSkill";

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL'];
const liveUrl = 'https://helpii.se'

export default function UserDetails( {params}: any ) {
    const userSettingsRef = useRef(null);
    const router = useRouter();
    const searchParams = useSearchParams()
    const {appResults, setAppResults, userDetails, setUserDetails, Authuser} = useAppContext()
    const [profileDetails, setProfileDetails] = useState(null)
    const [isLoading, setLoading] = useState(true)
    const [showModal, setShowModal] = useState(false);
    const [isVisible, setIsVisible] = useState(true);

    // console.log("Params : ", params);
    useEffect(() => {
        const fetchData = async () => {
            try {
                const data = {'slug':params}
                const config = { headers: { 'content-type': 'application/json' } };
                axios.post(getPublicUrl+'/api/profile-details', data, config).then(response => {
                    const searchResults = response
                    setUserDetails({ details: searchResults });
                    // setProfileDetails({ details: searchResults });
                })
            } catch (error) {
                console.error('Error fetching user details:', error);
            }
        };

        fetchData();
    }, [params])

    // const handleClose = () => setShowModal(false);
    // const handleShow = () => setShowModal(!showModal);

    const handleClick = (e) => {
        setIsVisible(!isVisible);
        if (userSettingsRef.current) {
            userSettingsRef.current.scrollIntoView({ behavior: 'smooth' });
        }
    };

    const countries: Array<TypeCountries> = [
        { value: 'en', label: 'English', flag: `${getPublicUrl}/images/helpii-user-settings/flags/english.svg` },
        { value: 'sv', label: 'Swedish', flag: `${getPublicUrl}/images/helpii-user-settings/flags/swedish.svg` },
        { value: 'de', label: 'German', flag: `${getPublicUrl}/images/helpii-user-settings/flags/german.svg` },
        { value: 'hr', label: 'Croatian', flag: `${getPublicUrl}/images/helpii-user-settings/flags/croatian.svg` },
        { value: 'es', label: 'Spanish', flag: `${getPublicUrl}/images/helpii-user-settings/flags/spanish.svg` },
        { value: 'hi', label: 'Hindi', flag: `${getPublicUrl}/images/helpii-user-settings/flags/bharat.svg` },
        { value: 'bs', label: 'Bosian', flag: `${getPublicUrl}/images/helpii-user-settings/flags/bosnian.svg` },
        { value: 'no', label: 'Norwegian', flag: `${getPublicUrl}/images/helpii-user-settings/flags/norwegian.svg` },
        { value: 'da', label: 'Danish', flag: `${getPublicUrl}/images/helpii-user-settings/flags/danish.svg` },
    ]

    const userData = userDetails ? userDetails.details.data : '';
    const checkAuth = Authuser ? Authuser.user : '';
    const userProfile = userData ? userData.alluserData.profile : null;

    const selectedLanguage = userProfile ? countries.find((country) => country.value === userProfile.language) : null;
    // console.log('Userss : ', userData);
    // console.log('Authuser : ', checkAuth);

    return (
        <>
            {userData && <>
                {userData.profile_badge && <>
                    <div className="badges-section"></div>
                </>}
                <div className={`profile-section user-profile-${userData.alluserData.id}`}>
                    <Container>
                        <Row>
                            <Col md={12}>
                                <div className="profile-wrapper d-flex align-items-center">
                                    <div className="flex-shrink-0">
                                        <img className="profileimage" src={liveUrl+'/'+userData.alluserData.avatar_type+'/'+userData.alluserData.avatar_location}  alt="profile-image"/>

                                        <ul className="profile-rating">
                                            <li>
                                              <img src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                            </li>
                                            <li>
                                              <img src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                            </li>
                                            <li>
                                              <img src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                            </li>
                                            <li>
                                              <img src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                            </li>
                                            <li>
                                              <img src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                            </li>
                                        </ul>
                                            <small className="feedback-text">Läs feedback</small>
                                    </div>
                                    <div className="flex-grow-1 profile-content">
                                        <div className="profile-header">
                                            <h4>{ userData.alluserData.full_name }</h4>
                                            <ul className={`profile-icons profile-icons-${userData.alluserData.id}`}>
                                                {checkAuth && checkAuth.id == userData.alluserData.id && <>
                                                    <li className={`edit-profile user-edit-profile-${userData.alluserData.id}`} onClick={(e) => handleClick(false)}>
                                                        <svg width="51" height="50" viewBox="0 0 51 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M19.1782 8.03516L20.7139 4.07087C20.9729 3.39967 21.4286 2.82234 22.0212 2.41449C22.6139 2.00663 23.3159 1.78728 24.0353 1.78516H26.9639C27.6833 1.78728 28.3853 2.00663 28.978 2.41449C29.5707 2.82234 30.0263 3.39967 30.2853 4.07087L31.821 8.03516L37.0353 11.0352L41.2496 10.3923C41.9513 10.297 42.6655 10.4126 43.3015 10.7242C43.9374 11.0358 44.4663 11.5294 44.821 12.1423L46.2496 14.6423C46.6157 15.265 46.7843 15.984 46.7333 16.7045C46.6823 17.425 46.414 18.1131 45.9639 18.678L43.3568 21.9994V27.9994L46.0353 31.3209C46.4855 31.8858 46.7537 32.5739 46.8047 33.2944C46.8558 34.0149 46.6871 34.7339 46.321 35.3566L44.8925 37.8566C44.5377 38.4695 44.0088 38.9631 43.3729 39.2747C42.737 39.5863 42.0228 39.7018 41.321 39.6066L37.1068 38.9637L31.8925 41.9637L30.3567 45.928C30.0978 46.5992 29.6421 47.1765 29.0494 47.5844C28.4568 47.9922 27.7548 48.2116 27.0353 48.2137H24.0353C23.3159 48.2116 22.6139 47.9922 22.0212 47.5844C21.4286 47.1765 20.9729 46.5992 20.7139 45.928L19.1782 41.9637L13.9639 38.9637L9.74961 39.6066C9.04788 39.7018 8.33368 39.5863 7.69776 39.2747C7.06184 38.9631 6.53291 38.4695 6.17818 37.8566L4.74961 35.3566C4.38354 34.7339 4.21489 34.0149 4.26589 33.2944C4.3169 32.5739 4.58518 31.8858 5.03532 31.3209L7.64247 27.9994V21.9994L4.96389 18.678C4.51375 18.1131 4.24547 17.425 4.19447 16.7045C4.14346 15.984 4.31211 15.265 4.67818 14.6423L6.10675 12.1423C6.46148 11.5294 6.99041 11.0358 7.62633 10.7242C8.26225 10.4126 8.97645 10.297 9.67818 10.3923L13.8925 11.0352L19.1782 8.03516ZM18.3568 24.9994C18.3568 26.4122 18.7757 27.7932 19.5605 28.9678C20.3454 30.1424 21.461 31.058 22.7662 31.5986C24.0713 32.1392 25.5075 32.2807 26.8931 32.0051C28.2787 31.7294 29.5514 31.0491 30.5504 30.0502C31.5493 29.0513 32.2296 27.7785 32.5052 26.3929C32.7808 25.0074 32.6394 23.5712 32.0987 22.266C31.5581 20.9608 30.6426 19.8452 29.468 19.0604C28.2933 18.2755 26.9123 17.8566 25.4996 17.8566C23.6052 17.8566 21.7884 18.6091 20.4488 19.9487C19.1093 21.2882 18.3568 23.105 18.3568 24.9994Z" stroke="#873D8F" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                        </svg>
                                                    </li>
                                                </>}
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="40" viewBox="0 0 45 40" fill="none">
                                                        <path d="M22.5 39.5L41.599 20.3617C43.792 18.1642 45 15.2422 45 12.1349C45 9.02699 43.792 6.1055 41.599 3.90799C39.406 1.71048 36.4905 0.5 33.389 0.5C30.2875 0.5 27.372 1.71048 25.179 3.90799L22.5 6.5925L19.821 3.90799C17.628 1.71048 14.712 0.5 11.611 0.5C8.50949 0.5 5.594 1.71048 3.401 3.90799C1.2075 6.1055 0 9.02699 0 12.1349C0 15.2427 1.2075 18.1642 3.401 20.3617L22.5 39.5ZM33.389 2.50411C35.956 2.50411 38.3695 3.50617 40.185 5.3249C42.0005 7.14363 43 9.56259 43 12.1349C43 14.7071 42 17.1256 40.185 18.9448L22.5 36.6662L4.815 18.9448C2.9995 17.1256 2 14.7071 2 12.1349C2 9.56259 2.9995 7.14413 4.815 5.3249C6.6305 3.50617 9.0435 2.50411 11.611 2.50411C14.178 2.50411 16.5915 3.50617 18.407 5.3249L22.5 9.42681L26.5935 5.3249C28.4085 3.50617 30.822 2.50411 33.389 2.50411Z" fill="#873D8F"/>
                                                    </svg>
                                                </li>
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                                                      <path
                                                        d="M38.071 30.8344C35.6728 30.8344 33.5314 31.9628 32.1446 33.7124L20.846 27.4012C21.1005 26.6433 21.2396 25.8293 21.2396 24.9907C21.2396 24.1435 21.1004 23.3369 20.8378 22.5713L32.1269 16.2692C33.5057 18.0275 35.6557 19.1643 38.0624 19.1643C42.2184 19.1643 45.6115 15.78 45.6115 11.6151C45.6115 7.45035 42.2272 4.06602 38.0624 4.06602C33.8976 4.06602 30.5132 7.45035 30.5132 11.6151C30.5132 12.4618 30.6523 13.2763 30.9148 14.0347L19.6348 20.3365C18.2558 18.5698 16.1056 17.4416 13.6991 17.4416C9.54306 17.4416 6.15 20.8258 6.15 24.9907C6.15 29.1556 9.54314 32.5398 13.7078 32.5398C16.1147 32.5398 18.2648 31.4028 19.6518 29.6361L30.941 35.9468C30.678 36.7131 30.5305 37.5359 30.5305 38.3835C30.5305 42.5396 33.9148 45.9327 38.0797 45.9327C42.2445 45.9327 45.6288 42.5483 45.6288 38.3835C45.6288 34.2186 42.2357 30.8344 38.071 30.8344ZM38.071 6.61063C40.8363 6.61063 43.0842 8.85856 43.0842 11.6238C43.0842 14.389 40.8363 16.637 38.071 16.637C35.3058 16.637 33.0579 14.389 33.0579 11.6238C33.0579 8.85867 35.3143 6.61063 38.071 6.61063ZM13.7078 30.0039C10.9425 30.0039 8.69462 27.7559 8.69462 24.9907C8.69462 22.2255 10.9425 19.9775 13.7078 19.9775C16.473 19.9775 18.7209 22.2255 18.7209 24.9907C18.7209 27.7558 16.4645 30.0039 13.7078 30.0039ZM38.071 43.3881C35.3058 43.3881 33.0579 41.1401 33.0579 38.3749C33.0579 35.6097 35.3058 33.3617 38.071 33.3617C40.8363 33.3617 43.0842 35.6097 43.0842 38.3749C43.0842 41.1401 40.8363 43.3881 38.071 43.3881Z" fill="#873D8F" stroke="#873D8F" strokeWidth="0.2"/>
                                                    </svg>
                                                </li>
                                            </ul>
                                        </div>
                                        {userData.alluserData.profile.about && <>
                                            <small>Om mig</small>
                                            <p>
                                                {userData.alluserData.profile.about}
                                            </p>
                                        </>}
                                        <div className="profile-button-wrapper">
                                            <button className="link1">
                                                <img className="mb-1" src={getPublicUrl+"/images/profile-button.png"} alt="profile-button"/>
                                                Fakturera utan företag
                                            </button>
                                            <button className="link2">
                                                <img className="mb-1" src={getPublicUrl+"/images/send.png"} alt="profile-button"/>
                                                SEND PM
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </Col>
                        </Row>
                    </Container>
                </div>

                {/*User Skill and Ads*/}
                <div className="profile-maintitle" style={{ display: isVisible ? 'block' : 'none' }}>
                    <Container>
                        <Row>
                            <Col md={12}>
                                <div className="d-flex justify-content-between align-items-center">
                                    <h4>Mina Skills</h4>
                                    {checkAuth && checkAuth.id == userData.alluserData.id && <>
                                        {/*<button type={`button`} className="add-button">
                                            <img src={getPublicUrl+"/images/plus-icon.png"} alt="profile-button" />Nytt
                                        </button>*/}
                                        <CreateSkill userDetails={userData.alluserData}/>
                                    </>}
                                </div>
                            </Col>
                        </Row>
                    </Container>
                </div>
                <div className={`profile-skill user-skill-${userData.alluserData.id}`} style={{ display: isVisible ? 'block' : 'none' }}>
                    <Container>
                      <Row>
                      {userData.skills.length > 0 ? userData.skills.map((skill, index) => {
                        var price = '';
                        var time = '';
                        if( skill.show_price == 'hour') {
                            price = skill.price_per_hour;
                        } else {
                            price = skill.price_per_day;
                        }
                        return(
                            <Col md={6} className="mb-3" key={index}>
                                <Card className="profile-skill-card">
                                    <Card.Body className="p-0">
                                        <Card.Header>
                                            <h5 className="mb-0 user-skill-title">
                                                <img className="me-2" src={getPublicUrl+"/storage/skills/"+skill.avatar} alt={skill.name}/>
                                                {skill.name}
                                            </h5>
                                            {checkAuth && checkAuth.id == userData.alluserData.id && <>
                                                <ul className="profile-icons-wrap">
                                                    <li>
                                                        <img src={getPublicUrl+"/images/add-icon.png"} alt="add-icon" />
                                                    </li>
                                                    <li>
                                                        <img src={getPublicUrl+"/images/delete-icon.png"} alt="delete-icon"/>
                                                    </li>
                                                </ul>
                                            </>}
                                        </Card.Header>
                                        <Card.Text>
                                            {skill.description}
                                        </Card.Text>
                                        <p className="locationtext">
                                            <svg className="me-2" xmlns="http://www.w3.org/2000/svg" width="28" height="29" viewBox="0 0 28 29" fill="none">
                                              <path d="M4.66602 12.6118C4.66602 7.36485 8.84469 3.11133 13.9993 3.11133C19.154 3.11133 23.3327 7.36485 23.3327 12.6118C23.3327 17.8177 20.3538 23.8925 15.7061 26.0648C14.6226 26.5713 13.376 26.5713 12.2926 26.0648C7.64489 23.8925 4.66602 17.8177 4.66602 12.6118Z" stroke="#7A7A7A" strokeWidth="1.2"/>
                                              <circle cx="14" cy="12.4434" r="3.5" stroke="#7A7A7A" strokeWidth="1.2"/>
                                            </svg>
                                            {skill.address}
                                        </p>
                                    </Card.Body>
                                    <Card.Footer className="d-flex justify-content-end bg-transparent border-0 p-0">
                                        <h6 className="price">
                                            {price} <sub>kr / Timme</sub>
                                        </h6>
                                    </Card.Footer>
                                </Card>
                            </Col>
                        );
                      }): <h1>No skills found</h1>}
                      </Row>
                    </Container>
                </div>
                <div className="profile-maintitle" style={{ display: isVisible ? 'block' : 'none' }}>
                    <Container>
                        <Row>
                            <Col md={12}>
                                <div className="d-flex justify-content-between align-items-center">
                                    <h4>Mina Annonser</h4>
                                    {checkAuth && checkAuth.id == userData.alluserData.id && <>
                                        <button className="add-button">
                                            <img src={getPublicUrl+"/images/plus-icon.png"} alt="profile-button" />Nytt
                                        </button>
                                    </>}
                                </div>
                            </Col>
                        </Row>
                    </Container>
                </div>
                <div className={`profile-skill user-ads-${userData.alluserData.id}`} style={{ display: isVisible ? 'block' : 'none' }}>
                    <Container>
                        <Row>
                            {userData.UserAds.length > 0 ? userData.UserAds.map((ads, index) => {
                                var price = '';
                                var time = '';
                                if( ads.show_price == 'hour') {
                                    price = ads.price_per_hour;
                                    time = 'Timme';
                                } else if( ads.show_price == 'both' ) {
                                    price = ads.price_per_day+'-'+ads.price_per_hour;
                                    time = 'Dag - Timme';
                                } else {
                                    price = ads.price_per_day;
                                    time = 'Dag';
                                }
                                return(
                                    <Col md={6} className="mb-3" key={index}>
                                        <Card className="profile-skill-card">
                                            <Card.Body className="p-0">
                                                <Card.Header>
                                                    <h5 className="mb-0 user-ad-title">
                                                        <img className="me-2" src={getPublicUrl+"/storage/skills/"+ads.avatar} alt="painter-icon"/>
                                                        {ads.name}
                                                    </h5>
                                                    {checkAuth && checkAuth.id == userData.alluserData.id && <>
                                                        <ul className="profile-icons-wrap">
                                                            <li>
                                                                <img src={getPublicUrl+"/images/add-icon.png"} alt="add-icon" />
                                                            </li>
                                                            <li>
                                                                <img src={getPublicUrl+"/images/delete-icon.png"} alt="delete-icon"/>
                                                            </li>
                                                        </ul>
                                                    </>}
                                                </Card.Header>
                                                <Card.Text>
                                                    {ads.description ? ads.description : ''}
                                                </Card.Text>
                                                <p className="locationtext">
                                                    <svg className="me-2" xmlns="http://www.w3.org/2000/svg" width="28" height="29" viewBox="0 0 28 29" fill="none">
                                                        <path d="M4.66602 12.6118C4.66602 7.36485 8.84469 3.11133 13.9993 3.11133C19.154 3.11133 23.3327 7.36485 23.3327 12.6118C23.3327 17.8177 20.3538 23.8925 15.7061 26.0648C14.6226 26.5713 13.376 26.5713 12.2926 26.0648C7.64489 23.8925 4.66602 17.8177 4.66602 12.6118Z" stroke="#7A7A7A" strokeWidth="1.2"/>
                                                        <circle cx="14" cy="12.4434" r="3.5" stroke="#7A7A7A" strokeWidth="1.2"/>
                                                    </svg>
                                                    {ads.address+', '+ads.state+', '+ads.country}
                                                </p>
                                            </Card.Body>
                                            <Card.Footer className="d-flex justify-content-end bg-transparent border-0 p-0">
                                                <h6 className="price">
                                                    {price} <sub>kr / {time}</sub>
                                                </h6>
                                            </Card.Footer>
                                        </Card>
                                    </Col>
                                )
                            }) : <h1>No ads found</h1>}

                        </Row>
                    </Container>
                </div>

                {/*User Settings*/}
                <div ref={userSettingsRef} className="user-settings" style={{ display: isVisible != true ? 'block' : 'none' }}>
                    <Container>
                        <Row className={``}>
                            <Col md={12}>
                                <div className="user-settings-head d-flex align-items-center">
                                    <span className={`user-setting-back-btn me-4`} onClick={() => handleClick(true)}><img src={`${getPublicUrl}/images/right-caret.svg`} alt="Left" /> Back</span>
                                    <h4>Mina Inställningar</h4>
                                </div>
                            </Col>
                        </Row>
                        <Row className={`justify-content-center`}>
                            <Col md={8}>
                                <ul className="user-profile-settings-list mb-2">
                                    <li className={`user-language-li`}>
                                        <div className={`user-language`}>
                                            <img src={`${getPublicUrl}/images/helpii-user-settings/user-language.svg`} alt="Language Setting" />
                                            {selectedLanguage && (
                                                <>
                                                    <span className={`ms-4`}>
                                                        <img src={`${selectedLanguage.flag}`} width={`49px`} height={`35px`} alt="Bharat" />
                                                        <span className={`user-settings-nm ms-3`}>{selectedLanguage.label}</span>
                                                    </span>
                                                </>
                                            )}
                                        </div>
                                        <div className={`edit-setting`} onClick={() => setShowLanguageModal(true)}>
                                            <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                                        </div>
                                    </li>
                                    <li className={`user-notification-li`}>
                                        <div className={`user-notification`}>
                                            <img src={`${getPublicUrl}/images/helpii-user-settings/user-notification.svg`} alt="Notification Setting" />
                                            <span className={`user-settings-nm ms-4`}>Säkerhet</span>
                                        </div>
                                        <div className={`edit-setting`}>
                                            <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                                        </div>
                                    </li>
                                    <li className={`user-delete-profile-li`}>
                                        <div className={`user-delete-profile`}>
                                            <img src={`${getPublicUrl}/images/helpii-user-settings/delete-profile.png`} alt="Delete Profile" />
                                            <span className={`user-settings-nm ms-4`}>Ta bort min profil</span>
                                        </div>
                                        <div className={`edit-setting`}>
                                            <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                                        </div>
                                    </li>
                                    <li className={`user-currency-li`}>
                                        <div className={`user-currency`}>
                                            <img src={`${getPublicUrl}/images/helpii-user-settings/user-currency.svg`} alt="Delete Profile" />
                                            <span className={`user-settings-nm ms-4`}>kr</span>
                                        </div>
                                        <div className={`edit-setting`}>
                                            <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                                        </div>
                                    </li>
                                    <li className={`user-availability-li`}>
                                        <div className={`user-availability`}>
                                            <img src={`${getPublicUrl}/images/helpii-user-settings/user-availability.svg`} alt="Delete Profile" />
                                            <span className={`user-settings-nm ms-4`}>Uppdatera min tillgänglighet</span>
                                        </div>
                                        <div className={`edit-setting`} onClick={() => setShowAvailabilityModal(true)}>
                                            <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                                        </div>
                                    </li>
                                    <li className={`user-info-li`}>
                                        <div className={`user-info`}>
                                            <img src={`${getPublicUrl}/images/helpii-user-settings/user-info.svg`} alt="Delete Profile" />
                                            <span className={`user-settings-nm ms-4`}>Uppdatera profilinformation</span>
                                        </div>
                                        <div className={`edit-setting`} onClick={() => setShowUpdateInfoModal(true)}>
                                            <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                                        </div>
                                    </li>
                                    <li className={`user-badge-li`}>
                                        <div className={`user-badge`}>
                                            <img src={`${getPublicUrl}/images/helpii-user-settings/user-badge.svg`} alt="Delete Profile" />
                                            <span className={`user-settings-nm ms-4`}>Jag fakturerar med FF</span>
                                        </div>
                                        <div className={`edit-setting`}>
                                            <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                                        </div>
                                    </li>
                                </ul>
                                <span className={`user-setting-back-btn me-4`} onClick={() => handleClick(true)}><img src={`${getPublicUrl}/images/right-caret.svg`} alt="Left" /> Back</span>
                            </Col>
                        </Row>
                    </Container>
                </div>
            </>}
        </>
    );
};