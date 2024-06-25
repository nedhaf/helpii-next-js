"use client"
import React, { useState, useEffect } from 'react';
import axios from 'axios'
import { Link } from 'react-router-dom';
import { Container, Row, Col, Modal, Form, Button, Card } from 'react-bootstrap';
import SearchModal from '@/components/Modals/SearchModal';
import { useRouter, useParams, useSearchParams } from 'next/navigation'
import OwlCarousel from 'react-owl-carousel';
import "owl.carousel/dist/assets/owl.carousel.css";
import "owl.carousel/dist/assets/owl.theme.default.css";
import {useAppContext} from '@/context'
import '@/app/style/bootstrap.css'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL'];

// const FilterUsers = () => {
export default function FilterUsers(props) {
    const router = useRouter();
    const searchParams = useSearchParams()
    const {appResults, setAppResults, userDetails, setUserDetails} = useAppContext()

    const [queryParams, setQueryParams] = useState([]);
    const [filterResults, setFilterResults] = useState(null);
    //const searchResults = router;
    const searchTerms = localStorage.getItem('searchterms')

    const skillid = searchParams.get('skillid')
    const profile_badge = searchParams.get('profile_badge')
    const where = searchParams.get('where')
    const oldwhere = searchParams.get('oldwhere')
    const street_number = searchParams.get('street_number')
    const routes = searchParams.get('route')
    const locality = searchParams.get('locality')
    const administrative_area_level_1 = searchParams.get('administrative_area_level_1')
    const country = searchParams.get('country')
    const postal_code = searchParams.get('postal_code')
    const lng = searchParams.get('lng')
    const lat = searchParams.get('lat')
    const search_for = searchParams.get('search_for')

    const [showModal, setShowModal] = useState(false);


    const handleShowModal = () => {
        setShowModal(true);

        console.log(showModal, 'setShowModal');
    };

    const handleCloseModal = () => {
        setShowModal(false);
    };

    const [currentSlideIndex, setCurrentSlideIndex] = useState(0);
    const handleSlideChange = (event, newIndex) => {
        setCurrentSlideIndex(newIndex);
    };

    const handleDetailsPage = (e, userId) => {
        e.preventDefault(); // Prevent default behavior
        console.log('UserId : ', userId);
        const data = {'slug':userId}
         const config = { headers: { 'content-type': 'application/json' } };
        // axios.post(getPublicUrl+'/api/profile-details', data, config).then(response => {
        //     const searchResults = response
        //     setUserDetails({ details: searchResults });
        // })
        // router.push('/user-details/'+userId);
        router.push('/user-profile/'+userId);
    };

    const options = {
        margin: 30,
        responsiveClass: true,
        nav: false,
        dots: true,
        autoplay: false,
        navText: false,
        smartSpeed: 1000,
        responsive: {
            0: {
                items: 1,
            },
            768: {
                items: 3,
            },
            1200: {
                items: 1

            }
        },
    };

    const getResults = appResults ? appResults.results.data.data : '';
    const getErrors = appResults ? appResults.results.data.errors : '';
    return (
        <>
            <div className="filter-section">
                <Container>
                    <Row className="justify-content-center">
                        <Col lg={10}>
                            <h4>Sök profiler</h4>
                            <SearchModal />
                        </Col>
                    </Row>
                </Container>
            </div>
            <div className="advertise-section">
                <Container>
                    <Row className="justify-content-center">
                        <Col lg={10}>
                            <Row>
                                <Col lg={6}>
                                    <Card className="advertise-card">
                                        <Row>
                                            <Col md={4}>
                                                <span className="adv-tag">Annons</span>
                                                <Card.Img className="advertise-image"  src={getPublicUrl+"/images/adv-logo.png"} alt="profile-logo" />
                                            </Col>
                                            <Col md={8} className="advertise-content">
                                                <Card.Body>
                                                    <Card.Title>helpii<img src={getPublicUrl+"/images/painter-icon-3x.png"} alt="painter-icon" /></Card.Title>
                                                    <Card.Text>
                                                        Använd proffs till din vardag. Vi hjälper till förmånliga priser. Vi kommer och målar ett rum gratis när du ansöker om offert på ett helt hus.
                                                    </Card.Text>
                                                </Card.Body>
                                            </Col>
                                            <Col md={12}>
                                                <Card.Footer className="text-muted">
                                                    <div className="adv-link">
                                                        <img src={getPublicUrl+"/images/link-icon.png"} alt="link-icon" />
                                                        <a href="https://www.helpii.se" target="_blank">www.helpii.se</a>
                                                    </div>
                                                    <div className="adv-info">
                                                        <span>Tel: 123-123 55</span>
                                                        <img src={getPublicUrl+"/images/adv-helpii-logo.png"} alt="link-icon" />
                                                    </div>
                                                </Card.Footer>
                                            </Col>
                                        </Row>
                                    </Card>
                                </Col>
                                <Col lg={6}>
                                    <Card className="advertise-card">
                                    <Row>
                                        <Col md={4}>
                                            <span className="adv-tag">Annons</span>
                                            <Card.Img className="advertise-image"  src={getPublicUrl+"/images/adv-logo.png"} alt="profile-logo" />
                                        </Col>
                                        <Col md={8} className="advertise-content">
                                            <Card.Body>
                                                <Card.Title>helpii<img src={getPublicUrl+"/images/painter-icon-3x.png"} alt="painter-icon" /></Card.Title>
                                                <Card.Text>
                                                    Använd proffs till din vardag. Vi hjälper till förmånliga priser. Vi kommer och målar ett rum gratis när du ansöker om offert på ett helt hus.
                                                </Card.Text>
                                            </Card.Body>
                                        </Col>
                                        <Col md={12}>
                                        <Card.Footer className="text-muted">
                                            <div className="adv-link">
                                                <img src={getPublicUrl+"/images/link-icon.png"} alt="link-icon" />
                                                <a href="https://www.helpii.se" target="_blank">www.helpii.se</a>
                                            </div>
                                            <div className="adv-info">
                                                <span>Tel: 123-123 55</span>
                                                <img src={getPublicUrl+"/images/adv-helpii-logo.png"} alt="link-icon" />
                                            </div>
                                        </Card.Footer>
                                        </Col>
                                    </Row>
                                </Card>
                                </Col>
                            </Row>
                        </Col>
                    </Row>
                </Container>
            </div>
            {getResults && <>
                <div className="skill-section">
                    <Container>
                        <Row className="justify-content-center">
                            <Col lg={10}>
                                <Row>
                                    {getResults.results[0].length > 0 ? getResults.results[0].map((user, index) => {
                                        const liveUrl = 'https://helpii.se'
                                        var storageIndex = user.sp_image.indexOf('/storage/');
                                        var userImg = '';
                                        if (user.sp_image.length > storageIndex + '/storage/'.length) {
                                            // If image name exists after '/storage/', return the original URL
                                            userImg = liveUrl+user.sp_image;
                                        } else {
                                            // If no image name exists, add the dummy image name
                                            userImg = getPublicUrl+user.sp_image+'avatars/dummy.png';
                                        }
                                        return (
                                            <Col lg={6} key={index}>
                                                <OwlCarousel margin={10} className="skill-slider" {...options} key={index}>
                                                { user.sp_user_all_skills.length > 1 ? user.sp_user_all_skills.map((userskills, idx) => {
                                                    console.log('Searched Skill user : ', userskills);
                                                    return (
                                                        <Card className={`skill-card skill-card-${idx} skill-user-id`} key={idx}>
                                                            <Row>
                                                                <Col md={4}>
                                                                    <div className="skill-profile-image"  alt="profile-image">
                                                                        <img src={userImg} alt={user.sp_name} />
                                                                    </div>
                                                                </Col>
                                                                <Col md={8} className="skill-content" >
                                                                    <Card.Body>
                                                                        <Card.Title >
                                                                            <a onClick={(e) => handleDetailsPage(e, user.sp_slug)}>
                                                                                {user.sp_name}
                                                                            </a>
                                                                            {/*<img src={getPublicUrl+"/images/skill-profile-icon-01.png"} alt="skill-profile-icon" />*/}
                                                                        </Card.Title>
                                                                        <ul className={`skillicon-list skillicon-list-${idx}`}>
                                                                            { user.sp_user_all_skills.length > 1 ? user.sp_user_all_skills.map((skills, sindx) => {
                                                                                // console.log('Serched Skills : ', skills);
                                                                                var price = '';
                                                                                var time = '';
                                                                                var isFree = 0;
                                                                                if( skills.s_price_type == 'hour' ) {
                                                                                    if( skills.s_price_per_hour && skills.s_price_per_hour != '0.00' && skills.s_price_per_hour != '0' ) {
                                                                                        isFree = 0;
                                                                                        const perHourPrice = Math.round(skills.s_price_per_hour) === skills.s_price_per_hour ? skills.s_price_per_hour : Math.round(skills.s_price_per_hour);
                                                                                        price = perHourPrice;
                                                                                        time = 'Timme';
                                                                                    } else {
                                                                                        isFree = 1;
                                                                                        price = 'Free';
                                                                                    }
                                                                                } else if( skills.s_price_type == 'day' ) {
                                                                                    if( skills.s_price_per_day && skills.s_price_per_day != '0.00' && skills.s_price_per_day != '0' ) {
                                                                                        isFree = 0;
                                                                                        const perDayPrice = Math.round(skills.s_price_per_day) === skills.s_price_per_day ? skills.s_price_per_day : Math.round(skills.s_price_per_day);
                                                                                        price = perDayPrice;
                                                                                        time = 'Dag';
                                                                                    } else {
                                                                                        isFree = 1;
                                                                                        price = 'Free';
                                                                                    }
                                                                                } else {
                                                                                    if( ( skills.s_price_per_hour && skills.s_price_per_hour != '0.00' && skills.s_price_per_hour != '0' ) && ( skills.s_price_per_day && skills.s_price_per_day != '0.00' && skills.s_price_per_day != '0' ) ) {
                                                                                        isFree = 0;
                                                                                        const perHourPrice = Math.round(skills.s_price_per_hour) === skills.s_price_per_hour ? skills.s_price_per_hour : Math.round(skills.s_price_per_hour);
                                                                                        const perDayPrice = Math.round(skills.s_price_per_day) === skills.s_price_per_day ? skills.s_price_per_day : Math.round(skills.s_price_per_day);
                                                                                        price = perHourPrice+' - '+perDayPrice;
                                                                                        time = 'Timme - Dag';
                                                                                    } else {
                                                                                        isFree = 1;
                                                                                        price = 'Free';
                                                                                    }
                                                                                }
                                                                                if (sindx === idx) {
                                                                                    return(
                                                                                        <li key={sindx} className={`skill-list-li skill-list-li-${sindx} skill-id-${skills.ID}`}><img src={getPublicUrl+skills.s_avatar} alt="delivery-skill" />
                                                                                            <div className={`arrow`}>
                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="7" viewBox="0 0 9 7" fill="none">
                                                                                                    <path d="M3.63397 0.500001C4.01887 -0.166666 4.98113 -0.166667 5.36603 0.5L7.9641 5C8.349 5.66667 7.86788 6.5 7.09808 6.5H1.90192C1.13212 6.5 0.650998 5.66667 1.0359 5L3.63397 0.500001Z" fill="#873D8F"/>
                                                                                                </svg>
                                                                                            </div>
                                                                                            <div className="skill-price-tag">
                                                                                                {price} { !isFree ? (<><small className="currency">SEK/{time}</small></>) : null}
                                                                                            </div>
                                                                                        </li>
                                                                                    );
                                                                                } else {
                                                                                    return (
                                                                                        <li key={sindx} className={`skill-list-li skill-list-li-${sindx} skill-id-${skills.ID}`}><img src={getPublicUrl+skills.s_avatar} alt="delivery-skill" /></li>
                                                                                    );
                                                                                }
                                                                            }):''}
                                                                        </ul>
                                                                    </Card.Body>
                                                                </Col>
                                                                <Col md={12}>
                                                                    <Card.Footer className="text-muted">
                                                                        { user.sp_user_all_skills.length > 1 ? user.sp_user_all_skills.map((skills, sindx) => {
                                                                            if (sindx === idx) {
                                                                                return(
                                                                                    <div className="location">
                                                                                        <img src={getPublicUrl+"/images/skill-location-icon.png"} alt="skill-location-icon" />
                                                                                        {skills.s_city+', '+skills.s_state+', '+skills.s_country}
                                                                                    </div>

                                                                                );
                                                                            }
                                                                        }):''}
                                                                        {/*<div className="location">
                                                                            <img src={getPublicUrl+"/images/skill-location-icon.png"} alt="skill-location-icon" />
                                                                            Jönköping, Husqvarna
                                                                        </div>
                                                                        <div className="skill-rating">
                                                                            <img className="" src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                                                            4.5
                                                                        </div>*/}
                                                                    </Card.Footer>
                                                                </Col>
                                                            </Row>
                                                        </Card>
                                                    );
                                                }) :
                                                    <Card className="skill-card">
                                                        <Row>
                                                            <Col md={4}>
                                                               <div className="skill-profile-image"  alt="profile-image">
                                                                    <img src={userImg} alt={user.sp_name} />
                                                                </div>
                                                            </Col>
                                                            <Col md={8} className="skill-content">
                                                                <Card.Body>
                                                                    <Card.Title>
                                                                        <a onClick={(e) => handleDetailsPage(e, user.sp_slug)}>
                                                                            {user.sp_name}
                                                                        </a>
                                                                        {/*<img src={getPublicUrl+"/images/skill-profile-icon-01.png"} alt="skill-profile-icon" />*/}
                                                                    </Card.Title>
                                                                    <ul className="skillicon-list">
                                                                        { user.sp_user_all_skills.map((singleskills, ssindx) => {
                                                                            var price = '';
                                                                            var time = '';
                                                                            var isFree = 0;
                                                                            if( singleskills.s_price_type == 'hour' ) {
                                                                                if( singleskills.s_price_per_hour && singleskills.s_price_per_hour != '0.00' && singleskills.s_price_per_hour != '0' ) {
                                                                                    isFree = 0;
                                                                                    const perHourPrice = Math.round(singleskills.s_price_per_hour) === singleskills.s_price_per_hour ? singleskills.s_price_per_hour : Math.round(singleskills.s_price_per_hour);
                                                                                    price = perHourPrice;
                                                                                    time = 'Timme';
                                                                                } else {
                                                                                    isFree = 1;
                                                                                    price = 'Free';
                                                                                }
                                                                            } else if( singleskills.s_price_type == 'day' ) {
                                                                                if( singleskills.s_price_per_day && singleskills.s_price_per_day != '0.00' && singleskills.s_price_per_day != '0' ) {
                                                                                    isFree = 0;
                                                                                    const perDayPrice = Math.round(singleskills.s_price_per_day) === singleskills.s_price_per_day ? singleskills.s_price_per_day : Math.round(singleskills.s_price_per_day);
                                                                                    price = perDayPrice;
                                                                                    time = 'Dag';
                                                                                } else {
                                                                                    isFree = 1;
                                                                                    price = 'Free';
                                                                                }
                                                                            } else {
                                                                                if( ( singleskills.s_price_per_hour && singleskills.s_price_per_hour != '0.00' && singleskills.s_price_per_hour != '0' ) && ( singleskills.s_price_per_day && singleskills.s_price_per_day != '0.00' && singleskills.s_price_per_day != '0' ) ) {
                                                                                    isFree = 0;
                                                                                    const perHourPrice = Math.round(singleskills.s_price_per_hour) === singleskills.s_price_per_hour ? singleskills.s_price_per_hour : Math.round(singleskills.s_price_per_hour);
                                                                                    const perDayPrice = Math.round(singleskills.s_price_per_day) === singleskills.s_price_per_day ? singleskills.s_price_per_day : Math.round(singleskills.s_price_per_day);
                                                                                    price = perHourPrice+' - '+perDayPrice;
                                                                                    time = 'Timme - Dag';
                                                                                } else {
                                                                                    isFree = 1;
                                                                                    price = 'Free';
                                                                                }
                                                                            }
                                                                            return(
                                                                                <li key={ssindx} className={`skill-list-li skill-list-li-${ssindx} skill-id-${singleskills.ID}`}><img src={getPublicUrl+singleskills.s_avatar} alt="delivery-skill" />
                                                                                    <div className={`arrow`}>
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="7" viewBox="0 0 9 7" fill="none">
                                                                                            <path d="M3.63397 0.500001C4.01887 -0.166666 4.98113 -0.166667 5.36603 0.5L7.9641 5C8.349 5.66667 7.86788 6.5 7.09808 6.5H1.90192C1.13212 6.5 0.650998 5.66667 1.0359 5L3.63397 0.500001Z" fill="#873D8F"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <div className="skill-price-tag">
                                                                                        {price} { !isFree ? (<><small className="currency">SEK/{time}</small></>) : null}
                                                                                    </div>
                                                                                </li>
                                                                            );
                                                                        })}
                                                                    </ul>
                                                                </Card.Body>
                                                            </Col>
                                                            <Col md={12}>
                                                            <Card.Footer className="text-muted">
                                                                { user.sp_user_all_skills.map((singleskills, ssindx) => {
                                                                    return(
                                                                        <div className="location">
                                                                            <img src={getPublicUrl+"/images/skill-location-icon.png"} alt="skill-location-icon" />
                                                                            {singleskills.s_city+', '+singleskills.s_state+', '+singleskills.s_country}
                                                                        </div>
                                                                    );
                                                                })}
                                                                {/*<div className="skill-rating">
                                                                    <img className="" src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                                                    4.5
                                                                </div>*/}
                                                            </Card.Footer>
                                                            </Col>
                                                        </Row>
                                                    </Card>
                                                }
                                                </OwlCarousel>
                                            </Col>
                                        );
                                    }) : <h1>No users found</h1>}
                                </Row>
                            </Col>
                        </Row>
                    </Container>
                </div>
            </>}
        </>
    );
};

// export default FilterUsers;