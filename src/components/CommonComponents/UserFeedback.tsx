'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
// import axios from 'axios'
import axios from '@/lib/axios'
import { Container, Row, Button, Col, Modal, Form, Alert, Card } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import {useAppContext} from '@/context'
import Select from 'react-select';
import { Rating } from 'react-simple-star-rating'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

const options = [
  { value: 'chocolate', label: 'Chocolate' },
  { value: 'strawberry', label: 'Strawberry' },
  { value: 'vanilla', label: 'Vanilla' },
];

export default function UserFeedback({ userId, checkUser, userFeedbacks, userSkills }) {
    const {AuthToken} = useAppContext()
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [isSuccess, setIsSuccess] = useState(null);
    const [showUpdateFeedbackModal, setUpdateFeedbackModal] = useState(false);
    const [feedbackdata, setfeedbackdata] = useState(userFeedbacks);
    const [feedBackDescription, setFeedBackDescription] = useState(null);
    const [hoveredValueForMoneyIndex, setHoveredValueForMoneyIndex] = useState(1);
    const [ratingValueForMoney, setRatingValueForMoney] = useState(null);
    const [hoveredRelationWithCustomerIndex, setHoveredRelationWithCustomerIndex] = useState(1);
    const [ratingRelationWithCustomer, setRatingRelationWithCustomer] = useState(null);
    const [hoveredQualityOfWorkIndex, setHoveredQualityOfWorkIndex] = useState(1);
    const [ratingQualityOfWork, setRatingQualityOfWork] = useState(null);
    const [hoveredPerformanceIndex, setPerformanceIndex] = useState(1);
    const [ratingPerformance, setPerformance] = useState(null);
    const [rating, setRating] = useState(0)

    const handleUpdateFeedbackModalShow = () => {
        setUpdateFeedbackModal(true)
        fetchSkills()
    };
    const handleUpdateFeedbackModalClose = () => setUpdateFeedbackModal(false);

    const [selectedOption, setSelectedOption] = useState();
    const [skillOptions, setSkillOptions] = useState(null);

    // const handleStarHover = (index, feedbackIndex) => {
    //     const updatedHoverRatings = [...hoverRatings]; // Create a copy
    //     updatedHoverRatings[feedbackIndex] = index + 1; // Update hover rating for specific feedback item
    //     setHoverRatings(updatedHoverRatings);

    //     console.log('Hover on stars : ', hoverRatings);
    // };

    // const ratingChanged = (newRating) => {
    //   console.log(newRating);
    // };

    const handleStarClick = (feedbackIndex, ratingFor) => {
        //
        switch (ratingFor) {
            case 'VFM':
                setRatingValueForMoney(feedbackIndex)
                break;
            case 'RWC':
                setRatingRelationWithCustomer(feedbackIndex)
                break;
            case 'QOW':
                setRatingQualityOfWork(feedbackIndex)
                break;
            case 'PRFRMS':
                setPerformance(feedbackIndex)
                break;
            default:
                return feedbackIndex
                break;
        }
        console.log('Click on stars : ', ratingFor);
    };

    useEffect(() => {
        // fetchFeedback();
    }, []);

    const fetchSkills = () => {
        let opts;
        const skillOpts = userSkills.map((skills, i) => {
            return { 'value': skills.skill.id, 'label': skills.skill.name };
        })
        setSkillOptions(skillOpts)
    }

    // Catch Rating value
    const handleRating = (rate: number, ratingFor: string) => {
        // console.log('Ratings for : ', ratingFor);
        // console.log('Updated Ratings : ', rate);
        switch (ratingFor) {
            case 'VFM':
                setRatingValueForMoney(rate)
                break;
            case 'RWC':
                setRatingRelationWithCustomer(rate)
                break;
            case 'QOW':
                setRatingQualityOfWork(rate)
                break;
            case 'PRFRMS':
                setPerformance(rate)
                break;
            default:
                return rate
                break;
        }
        setRating(rate)
    }

    // Optinal callback functions
    const onPointerEnter = () => {
        console.log('Enter')
    }

    const onPointerLeave = () => {
        console.log('Leave')
    }

    const onPointerMove = (value: number, index: number) => {
        console.log(value, index)
    }

    const handleFeedBackDescription = (event) => {
        setFeedBackDescription(event.target.value);
    };
    // Update Badge
    async function handleUpdateFeedback(e) {
        e.preventDefault();
        // feedback-update
        const feedback_id = parseInt(e.target.elements.feedback_id.value);
        const sp_skill_id = selectedOption ? selectedOption.value : parseInt(e.target.elements.sp_skill_id.value);
        const reviews = feedBackDescription ? feedBackDescription : e.target.elements.ads_description.value;
        const value_for_money = ratingValueForMoney ? ratingValueForMoney : parseInt(e.target.elements.value_for_money.value);
        const quality_of_work = ratingQualityOfWork ? ratingQualityOfWork : parseInt(e.target.elements.quality_of_work.value);
        const relation_with_customer = ratingRelationWithCustomer ? ratingRelationWithCustomer : parseInt(e.target.elements.relation_with_customer.value);
        const performance = ratingPerformance ? ratingPerformance : parseInt(e.target.elements.performance.value);

        const formdata = {
            'uid':userId,
            'feedback_id':feedback_id,
            'sp_skill_id':sp_skill_id,
            'review': reviews,
            'value_for_money': value_for_money,
            'quality_of_work': quality_of_work,
            'relation_with_customer': relation_with_customer,
            'performance': performance,
        };
        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`}
        };

        await axios.post(getPublicUrl+'/api/feedback-update', formdata, config).then(response => {
            if( response.data.status == 200 ) {
                setIsSuccess(true);
                setSuccessMessage(response.data.message);
                setTimeout(function () {
                    // document.getElementById(`userCurrency-${userId}`).classList.remove("show", "d-block");
                    // document.querySelectorAll(".modal-backdrop").forEach(el => el.classList.remove("modal-backdrop"));
                    setSuccessMessage(null)
                    setSelectedOption(null)
                    setRatingValueForMoney(1)
                    setRatingRelationWithCustomer(1)
                    setRatingQualityOfWork(1)
                    setPerformance(1)
                    setFeedBackDescription(null)
                    setRating(1)
                }, 1500);
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

    return (
        <>
            <div className="profile-reviews">
                <Container>
                    <Row>
                        <Col md={12}>
                            <div className="d-flex justify-content-between align-items-center">
                                <div className="profile-reviews">
                                    <Container>
                                      <Row>
                                        {feedbackdata && feedbackdata.map((feedback, index) => {
                                            // console.log("feedback : ", feedback);
                                            let userProfileImage;
                                            let ratingDate = new Date(feedback.updated_at);
                                            let reviewReactions = feedback.reactions;

                                            let year = ratingDate.getFullYear();
                                            let month = ratingDate.getMonth()+1;
                                            let dt = ratingDate.getDate();
                                            if (dt < 10) { dt = '0' + dt;}
                                            if (month < 10) {month = '0' + month;}
                                            let ratingFinalDate = year+ '/' + month + '/'+ dt;

                                            if(feedback.avatar_location){
                                                userProfileImage = getPublicUrl+"/storage/"+feedback.avatar_location;
                                            }else{
                                                userProfileImage = getPublicUrl+"/storage/avatars/dummy-avatar.png";
                                            }
                                            const defaultSkillValue = skillOptions ? skillOptions.find((skillOption) => skillOption.value === feedback.skill.id) : null;
                                            // console.log('defaultSkillValue : ', defaultSkillValue);
                                            return(
                                                <>
                                                    <Col md={12} key={index}>
                                                        <Card className={`reviews-card review-user-${feedback.id}`}>
                                                            <Card.Body className="p-0">
                                                                <div className="reviews-main-wrapper d-flex justify-content-between align-items-center">
                                                                    <div className="d-flex align-items-center">
                                                                        <div className="flex-shrink-0 review-user-img">
                                                                            <img src={userProfileImage} alt="review-avatar"/>
                                                                        </div>
                                                                        <div className="flex-grow-1 ms-3">
                                                                            <h5>{feedback.first_name}&nbsp;{feedback.last_name}</h5>
                                                                            <p className="mb-0">Skill: <strong>{feedback.skill && feedback.skill.name ? feedback.skill.name : "-"}</strong></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="review-wrapper d-flex align-items-center">
                                                                        <img
                                                                            class="me-2"
                                                                            src={getPublicUrl+"/images/star-icon.png"}
                                                                            alt="star-icon"
                                                                        />
                                                                        <span>{Math.round(feedback.total)}</span>
                                                                    </div>
                                                                </div>
                                                                <div className={`sub-review-wrapper d-flex justify-content-between flex-grow-1`}>
                                                                    <div className={`review-value-for-money-wrapper`}>
                                                                        <div className={`review-value-for-money-section d-flex align-items-center`}>
                                                                            <h6 className={`reviews-inner-headings me-2 mb-0`}>Värde för pengar</h6>
                                                                            <ul className="sub-review-star-wrapper mb-1">
                                                                                {Array(5).fill(null).map((_, index) => (
                                                                                    <li key={index}>
                                                                                      <img src={feedback.value_for_money > index ? `${getPublicUrl}/images/star-icon.png` : `${getPublicUrl}/images/star-unfilled.png`} alt="star-icon"/>
                                                                                    </li>
                                                                                  ))}
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div className={`review-rel-with-customer-wrapper`}>
                                                                        <div className={`review-rel-with-customer-section d-flex align-items-center`}>
                                                                            <h6 className={`reviews-inner-headings me-2 mb-0`}>Relation med kunden</h6>
                                                                            <ul className="sub-review-star-wrapper mb-1">
                                                                                {Array(5).fill(null).map((_, index) => (
                                                                                    <li key={index}>
                                                                                      <img src={feedback.relation_with_customer > index ? `${getPublicUrl}/images/star-icon.png` : `${getPublicUrl}/images/star-unfilled.png`} alt="star-icon"/>
                                                                                    </li>
                                                                                  ))}
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div className={`review-quality-of-work-wrapper`}>
                                                                        <div className={`review-quality-of-work-section d-flex align-items-center`}>
                                                                            <h6 className={`reviews-inner-headings me-2 mb-0`}>Arbetskvalitet</h6>
                                                                            <ul className="sub-review-star-wrapper mb-1">
                                                                                {Array(5).fill(null).map((_, index) => (
                                                                                    <li key={index}>
                                                                                      <img src={feedback.quality_of_work > index ? `${getPublicUrl}/images/star-icon.png` : `${getPublicUrl}/images/star-unfilled.png`} alt="star-icon"/>
                                                                                    </li>
                                                                                  ))}
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div className={`review-performance-wrapper`}>
                                                                        <div className={`review-performance-section d-flex align-items-center`}>
                                                                            <h6 className={`reviews-inner-headings me-2 mb-0`}>Prestanda</h6>
                                                                            <ul className="sub-review-star-wrapper mb-1">
                                                                                {Array(5).fill(null).map((_, index) => (
                                                                                    <li key={index}>
                                                                                      <img src={feedback.performance > index ? `${getPublicUrl}/images/star-icon.png` : `${getPublicUrl}/images/star-unfilled.png`} alt="star-icon"/>
                                                                                    </li>
                                                                                  ))}
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <p className="review-text">
                                                                    {feedback.review}
                                                                </p>
                                                                <div className="d-flex justify-content-between align-items-center">
                                                                    <div class="d-flex align-items-center">
                                                                        {checkUser && checkUser.id == feedback.from_userid && <>
                                                                            {/*<span className="edit-review">Edit your review</span>*/}
                                                                            <button type="button" className="edit-review" onClick={handleUpdateFeedbackModalShow} data-bs-toggle="modal" data-bs-target={`#userUpdateFeedbackModal-${feedback.id}`}>Edit your review</button>

                                                                        </>}
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <div className={`user-review-reactions-wrapper d-flex`}>
                                                                            <div className={`user-review-reactions-section d-flex justify-content-center me-1`}>
                                                                                    <div className={`review-dislike-reaction-section mt-1 me-2`}>
                                                                                        <img class="me-2 review-reaction-icon" src={getPublicUrl+"/images/dislike-rating.png"} alt="calendar"/>
                                                                                        <span className={`review-reactions-count`}>{feedback.user_dislike_count ? feedback.user_dislike_count : 0}</span>
                                                                                    </div>
                                                                                    <div className={`review-like-reaction-section mt-1`}>
                                                                                        <img class="me-2 review-reaction-icon" src={getPublicUrl+"/images/like-rating.png"} alt="calendar"/>
                                                                                        <span className={`review-reactions-count`}>{feedback.user_like_count ? feedback.user_like_count : 0}</span>
                                                                                    </div>
                                                                            </div>
                                                                            {reviewReactions?.length > 0 && (
                                                                                <>
                                                                                    <div className={`review-reactions-users-section d-flex justify-content-center me-4`}>
                                                                                        <ul className="reactions-users-wrapper mb-1">
                                                                                            {reviewReactions && reviewReactions.map((reactions, index) => {
                                                                                                return (
                                                                                                    <>
                                                                                                        <li className={`reaction-avatar`} key={index} data-bs-toggle="tooltip" data-bs-placement="top" title={`${reactions.name}`}>
                                                                                                            <img src={`${getPublicUrl}${reactions.avatar_image}`} alt="star-icon"/>
                                                                                                        </li>
                                                                                                    </>
                                                                                                );
                                                                                            })}
                                                                                        </ul>
                                                                                    </div>
                                                                                </>
                                                                            )}
                                                                        </div>
                                                                        <div className={`user-review-date`}>
                                                                            <img class="me-2 calendar-icon" src={getPublicUrl+"/images/calendar.png"} alt="calendar"/>
                                                                            <span className="review-date">{ratingFinalDate}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </Card.Body>
                                                        </Card>
                                                    </Col>

                                                    {/*Edit - Update Feedback Modal*/}
                                                    <div className="modal fade" id={`userUpdateFeedbackModal-${feedback.id}`} tabIndex="-1" aria-labelledby={`userBadge-${userId}`} aria-hidden="true">
                                                        <div className="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                                                            <div className="modal-content">
                                                                <div className="modal-header">
                                                                    <h1 className="modal-title fs-5 text-center" id={`userUpdateFeedbackModal-${feedback.id}Label`}>Recension</h1>
                                                                    <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div className="modal-body">
                                                                    <Form onSubmit={handleUpdateFeedback}>
                                                                        <input type={`hidden`} name={`feedback_id`} value={feedback.id}/>
                                                                        <input type={`hidden`} name={`sp_skill_id`} value={feedback.sp_skill_id}/>
                                                                        <input type={`hidden`} name={`value_for_money`} value={feedback.value_for_money}/>
                                                                        <input type={`hidden`} name={`quality_of_work`} value={feedback.quality_of_work}/>
                                                                        <input type={`hidden`} name={`relation_with_customer`} value={feedback.relation_with_customer}/>
                                                                        <input type={`hidden`} name={`performance`} value={feedback.performance}/>
                                                                        <div className={`row mb-4`}>
                                                                            <div className={`col-md-12`}>
                                                                                <Select
                                                                                    placeholder={`Välj skicklighet`}
                                                                                    defaultValue={userSkills.map((dskills, i) => {
                                                                                        if( dskills.skill.id == feedback.skill.id ) {
                                                                                            return { 'value': dskills.skill.id, 'label': dskills.skill.name };
                                                                                        }
                                                                                    })}
                                                                                    onChange={setSelectedOption}
                                                                                    options={userSkills.map((skills, i) => {
                                                                                        return { 'value': skills.skill.id, 'label': skills.skill.name };
                                                                                    })}
                                                                                    isClearable={true}
                                                                                    isSearchable={true}
                                                                                  />
                                                                                {errors?.sp_skill_id && <p className="error-message">{errors.sp_skill_id[0]}</p>}
                                                                            </div>
                                                                        </div>
                                                                        <div className={`row mb-4`}>
                                                                            <div className={`col-md-6 d-flex align-items-center`}>
                                                                                <h6 className={`edit-reviews-headings me-2 mb-0`}>Värde för pengar</h6>
                                                                                <Rating
                                                                                    className={`test d-flex gap-5`}
                                                                                    onClick={e => handleRating(e, 'VFM')}
                                                                                    onPointerEnter={onPointerEnter}
                                                                                    onPointerLeave={onPointerLeave}
                                                                                    onPointerMove={onPointerMove}
                                                                                    initialValue={feedback.value_for_money}
                                                                                    emptyIcon={
                                                                                        <svg className="me-1" width="20" height="20" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M17.3804 1.6938C18.5248 -0.356827 21.4752 -0.35683 22.6196 1.6938L26.8988 9.36107C27.328 10.1301 28.0734 10.6717 28.9374 10.8422L37.5518 12.5426C39.8557 12.9974 40.7674 15.8033 39.1708 17.5255L33.2011 23.9645C32.6024 24.6103 32.3176 25.4867 32.4224 26.3611L33.4672 35.0793C33.7467 37.411 31.3598 39.1451 29.2286 38.1588L21.26 34.4711C20.4607 34.1012 19.5393 34.1012 18.74 34.4711L10.7714 38.1588C8.64021 39.1451 6.25334 37.411 6.53277 35.0793L7.57758 26.3611C7.68238 25.4867 7.39764 24.6103 6.79888 23.9645L0.829203 17.5255C-0.767401 15.8033 0.1443 12.9974 2.44822 12.5426L11.0626 10.8422C11.9266 10.6717 12.672 10.1301 13.1012 9.36107L17.3804 1.6938Z" fill="#9D9D9D"/>
                                                                                        </svg>
                                                                                    }
                                                                                    fillIcon={
                                                                                        <svg className="me-1" width="20" height="20" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M17.3804 1.6938C18.5248 -0.356827 21.4752 -0.35683 22.6196 1.6938L26.8988 9.36107C27.328 10.1301 28.0734 10.6717 28.9374 10.8422L37.5518 12.5426C39.8557 12.9974 40.7674 15.8033 39.1708 17.5255L33.2011 23.9645C32.6024 24.6103 32.3176 25.4867 32.4224 26.3611L33.4672 35.0793C33.7467 37.411 31.3598 39.1451 29.2286 38.1588L21.26 34.4711C20.4607 34.1012 19.5393 34.1012 18.74 34.4711L10.7714 38.1588C8.64021 39.1451 6.25334 37.411 6.53277 35.0793L7.57758 26.3611C7.68238 25.4867 7.39764 24.6103 6.79888 23.9645L0.829203 17.5255C-0.767401 15.8033 0.1443 12.9974 2.44822 12.5426L11.0626 10.8422C11.9266 10.6717 12.672 10.1301 13.1012 9.36107L17.3804 1.6938Z" fill="#FFCF55"/>
                                                                                        </svg>
                                                                                    }
                                                                                />
                                                                            </div>
                                                                            <div className={`col-md-6 d-flex align-items-center`}>
                                                                                <h6 className={`edit-reviews-headings me-2 mb-0`}>Relation med kunden</h6>
                                                                                <Rating
                                                                                    className={`test d-flex gap-5`}
                                                                                    onClick={e => handleRating(e, 'RWC')}
                                                                                    onPointerEnter={onPointerEnter}
                                                                                    onPointerLeave={onPointerLeave}
                                                                                    onPointerMove={onPointerMove}
                                                                                    initialValue={feedback.relation_with_customer}
                                                                                    emptyIcon={
                                                                                        <svg className="me-1" width="20" height="20" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M17.3804 1.6938C18.5248 -0.356827 21.4752 -0.35683 22.6196 1.6938L26.8988 9.36107C27.328 10.1301 28.0734 10.6717 28.9374 10.8422L37.5518 12.5426C39.8557 12.9974 40.7674 15.8033 39.1708 17.5255L33.2011 23.9645C32.6024 24.6103 32.3176 25.4867 32.4224 26.3611L33.4672 35.0793C33.7467 37.411 31.3598 39.1451 29.2286 38.1588L21.26 34.4711C20.4607 34.1012 19.5393 34.1012 18.74 34.4711L10.7714 38.1588C8.64021 39.1451 6.25334 37.411 6.53277 35.0793L7.57758 26.3611C7.68238 25.4867 7.39764 24.6103 6.79888 23.9645L0.829203 17.5255C-0.767401 15.8033 0.1443 12.9974 2.44822 12.5426L11.0626 10.8422C11.9266 10.6717 12.672 10.1301 13.1012 9.36107L17.3804 1.6938Z" fill="#9D9D9D"/>
                                                                                        </svg>
                                                                                    }
                                                                                    fillIcon={
                                                                                        <svg className="me-1" width="20" height="20" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M17.3804 1.6938C18.5248 -0.356827 21.4752 -0.35683 22.6196 1.6938L26.8988 9.36107C27.328 10.1301 28.0734 10.6717 28.9374 10.8422L37.5518 12.5426C39.8557 12.9974 40.7674 15.8033 39.1708 17.5255L33.2011 23.9645C32.6024 24.6103 32.3176 25.4867 32.4224 26.3611L33.4672 35.0793C33.7467 37.411 31.3598 39.1451 29.2286 38.1588L21.26 34.4711C20.4607 34.1012 19.5393 34.1012 18.74 34.4711L10.7714 38.1588C8.64021 39.1451 6.25334 37.411 6.53277 35.0793L7.57758 26.3611C7.68238 25.4867 7.39764 24.6103 6.79888 23.9645L0.829203 17.5255C-0.767401 15.8033 0.1443 12.9974 2.44822 12.5426L11.0626 10.8422C11.9266 10.6717 12.672 10.1301 13.1012 9.36107L17.3804 1.6938Z" fill="#FFCF55"/>
                                                                                        </svg>
                                                                                    }
                                                                                />
                                                                            </div>
                                                                        </div>
                                                                        <div className={`row mb-4`}>
                                                                            <div className={`col-md-6 d-flex align-items-center`}>
                                                                                <h6 className={`edit-reviews-headings me-2 mb-0`}>Arbetskvalitet</h6>
                                                                                <Rating
                                                                                    className={`test d-flex gap-5`}
                                                                                    onClick={e => handleRating(e, 'QOW')}
                                                                                    onPointerEnter={onPointerEnter}
                                                                                    onPointerLeave={onPointerLeave}
                                                                                    onPointerMove={onPointerMove}
                                                                                    initialValue={feedback.quality_of_work}
                                                                                    emptyIcon={
                                                                                        <svg className="me-1" width="20" height="20" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M17.3804 1.6938C18.5248 -0.356827 21.4752 -0.35683 22.6196 1.6938L26.8988 9.36107C27.328 10.1301 28.0734 10.6717 28.9374 10.8422L37.5518 12.5426C39.8557 12.9974 40.7674 15.8033 39.1708 17.5255L33.2011 23.9645C32.6024 24.6103 32.3176 25.4867 32.4224 26.3611L33.4672 35.0793C33.7467 37.411 31.3598 39.1451 29.2286 38.1588L21.26 34.4711C20.4607 34.1012 19.5393 34.1012 18.74 34.4711L10.7714 38.1588C8.64021 39.1451 6.25334 37.411 6.53277 35.0793L7.57758 26.3611C7.68238 25.4867 7.39764 24.6103 6.79888 23.9645L0.829203 17.5255C-0.767401 15.8033 0.1443 12.9974 2.44822 12.5426L11.0626 10.8422C11.9266 10.6717 12.672 10.1301 13.1012 9.36107L17.3804 1.6938Z" fill="#9D9D9D"/>
                                                                                        </svg>
                                                                                    }
                                                                                    fillIcon={
                                                                                        <svg className="me-1" width="20" height="20" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M17.3804 1.6938C18.5248 -0.356827 21.4752 -0.35683 22.6196 1.6938L26.8988 9.36107C27.328 10.1301 28.0734 10.6717 28.9374 10.8422L37.5518 12.5426C39.8557 12.9974 40.7674 15.8033 39.1708 17.5255L33.2011 23.9645C32.6024 24.6103 32.3176 25.4867 32.4224 26.3611L33.4672 35.0793C33.7467 37.411 31.3598 39.1451 29.2286 38.1588L21.26 34.4711C20.4607 34.1012 19.5393 34.1012 18.74 34.4711L10.7714 38.1588C8.64021 39.1451 6.25334 37.411 6.53277 35.0793L7.57758 26.3611C7.68238 25.4867 7.39764 24.6103 6.79888 23.9645L0.829203 17.5255C-0.767401 15.8033 0.1443 12.9974 2.44822 12.5426L11.0626 10.8422C11.9266 10.6717 12.672 10.1301 13.1012 9.36107L17.3804 1.6938Z" fill="#FFCF55"/>
                                                                                        </svg>
                                                                                    }
                                                                                />
                                                                            </div>
                                                                            <div className={`col-md-6 d-flex align-items-center`}>
                                                                                <h6 className={`edit-reviews-headings me-2 mb-0`}>Prestanda</h6>
                                                                                <Rating
                                                                                    className={`test d-flex gap-5`}
                                                                                    onClick={e => handleRating(e, 'PRFRMS')}
                                                                                    onPointerEnter={onPointerEnter}
                                                                                    onPointerLeave={onPointerLeave}
                                                                                    onPointerMove={onPointerMove}
                                                                                    initialValue={feedback.performance}
                                                                                    emptyIcon={
                                                                                        <svg className="me-1" width="20" height="20" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M17.3804 1.6938C18.5248 -0.356827 21.4752 -0.35683 22.6196 1.6938L26.8988 9.36107C27.328 10.1301 28.0734 10.6717 28.9374 10.8422L37.5518 12.5426C39.8557 12.9974 40.7674 15.8033 39.1708 17.5255L33.2011 23.9645C32.6024 24.6103 32.3176 25.4867 32.4224 26.3611L33.4672 35.0793C33.7467 37.411 31.3598 39.1451 29.2286 38.1588L21.26 34.4711C20.4607 34.1012 19.5393 34.1012 18.74 34.4711L10.7714 38.1588C8.64021 39.1451 6.25334 37.411 6.53277 35.0793L7.57758 26.3611C7.68238 25.4867 7.39764 24.6103 6.79888 23.9645L0.829203 17.5255C-0.767401 15.8033 0.1443 12.9974 2.44822 12.5426L11.0626 10.8422C11.9266 10.6717 12.672 10.1301 13.1012 9.36107L17.3804 1.6938Z" fill="#9D9D9D"/>
                                                                                        </svg>
                                                                                    }
                                                                                    fillIcon={
                                                                                        <svg className="me-1" width="20" height="20" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M17.3804 1.6938C18.5248 -0.356827 21.4752 -0.35683 22.6196 1.6938L26.8988 9.36107C27.328 10.1301 28.0734 10.6717 28.9374 10.8422L37.5518 12.5426C39.8557 12.9974 40.7674 15.8033 39.1708 17.5255L33.2011 23.9645C32.6024 24.6103 32.3176 25.4867 32.4224 26.3611L33.4672 35.0793C33.7467 37.411 31.3598 39.1451 29.2286 38.1588L21.26 34.4711C20.4607 34.1012 19.5393 34.1012 18.74 34.4711L10.7714 38.1588C8.64021 39.1451 6.25334 37.411 6.53277 35.0793L7.57758 26.3611C7.68238 25.4867 7.39764 24.6103 6.79888 23.9645L0.829203 17.5255C-0.767401 15.8033 0.1443 12.9974 2.44822 12.5426L11.0626 10.8422C11.9266 10.6717 12.672 10.1301 13.1012 9.36107L17.3804 1.6938Z" fill="#FFCF55"/>
                                                                                        </svg>
                                                                                    }
                                                                                />
                                                                            </div>
                                                                        </div>
                                                                        <div className={`row mb-4`}>
                                                                            <div className={`col-md-12`}>
                                                                                <textarea className="form-control" id="ads_description" rows="6" value={feedBackDescription} onChange={handleFeedBackDescription} placeholder={`Skriv din feedback här`}>{feedback.review}</textarea>
                                                                                {errors?.review && <p className="error-message">{errors.review[0]}</p>}
                                                                            </div>
                                                                        </div>
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
                                                                        <div className={`user-froms-btns d-flex justify-content-center`}>
                                                                            <div className={`col-md-6`}>
                                                                                <button type="submit" className="save-button outline-primary">Update</button>
                                                                            </div>
                                                                        </div>
                                                                    </Form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </>
                                            );
                                        })}

                                      </Row>
                                    </Container>
                                </div>
                            </div>
                        </Col>
                    </Row>
                </Container>

            </div>
        </>
    );
}