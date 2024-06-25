'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
import axios from 'axios'
// import axios from '@/lib/axios'
import { Container, Row, Button, Col, Modal, Form, Alert, Card } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import {useAppContext} from '@/context'
import Select from 'react-select'
import { Rating } from 'react-simple-star-rating'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function CreateFeedbackModal({ userId, checkUser, userSkills }) {
    const {AuthToken} = useAppContext()
    const [errors, setErrors] = useState([])
    const [successMessage, setSuccessMessage] = useState(null)
    const [isSuccess, setIsSuccess] = useState(null)
    const [showCreateFeedbackModal, setShowCreateFeedbackModal] = useState(false)
    const [selectedOption, setSelectedOption] = useState(null)
    const [ratingValueForMoney, setRatingValueForMoney] = useState(1);
    const [ratingRelationWithCustomer, setRatingRelationWithCustomer] = useState(1);
    const [ratingQualityOfWork, setRatingQualityOfWork] = useState(1);
    const [ratingPerformance, setPerformance] = useState(1);
    const [feedBackDescription, setFeedBackDescription] = useState(null)
    const [rating, setRating] = useState(0)

    const handleShowCreateFeedbackModal = () => {
        setShowCreateFeedbackModal(true)
    }

    const handleCloseCreateFeedbackModal = () => {
        setShowCreateFeedbackModal(false)
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
        // console.log('Enter')
    }

    const onPointerLeave = () => {
        // console.log('Leave')
    }

    const onPointerMove = (value: number, index: number) => {
        // console.log(value, index)
    }

    const handleFeedBackDescription = (event) => {
        setFeedBackDescription(event.target.value);
    };

    // Update Badge
    async function handleCreateFeedback(e) {
        e.preventDefault();
        const value_for_money = ratingValueForMoney;
        const quality_of_work = ratingQualityOfWork;
        const relation_with_customer = ratingRelationWithCustomer;
        const performance = ratingPerformance;
        const sp_skill_id = selectedOption ? selectedOption.value : 0;
        const reviews = feedBackDescription ? feedBackDescription : null;

        const formdata = {
            'from_userid':checkUser.id,
            'to_userid':userId,
            'sp_skill_id':sp_skill_id,
            'review': reviews,
            'value_for_money': value_for_money,
            'quality_of_work': quality_of_work,
            'relation_with_customer': relation_with_customer,
            'performance': performance,
        };

        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`,'Content-Type': 'application/json'}
        };

        await axios.post(getPublicUrl+'/api/feedback-create', formdata, config).then(response => {
            console.log('Form Response data : ', response);

            if( response.data.status == 200 ) {
                setIsSuccess(true);
                setSuccessMessage(response.data.message);
                setTimeout(function () {
                    setSuccessMessage(null)
                    setIsSuccess(false);
                    setShowCreateFeedbackModal(false)

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
            console.log('Form error data : ', error);
            if (error.response && error.response.status === 422) {
                // Handle validation errors specifically
                setErrors(error.response.data.errors); // Set errors state
            } else {
                setErrors();
                // Handle other errors (e.g., network errors)
            }
        })

        console.log('Create Feedback : ', formdata);
    }

    return (
        <>
            <button type={`button`} className="add-button" onClick={handleShowCreateFeedbackModal}>
                <img src={getPublicUrl+"/images/plus-icon.png"} alt="profile-button" />Nytt
            </button>

            {/*Create Feedback Modal Start*/}
            <Modal className="create-feedback-popup" size="lg" aria-labelledby="contained-modal-title-vcenter" centered show={showCreateFeedbackModal} onHide={handleCloseCreateFeedbackModal}>
                <Modal.Header closeButton>
                    <Modal.Title>Modal heading</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Form onSubmit={handleCreateFeedback}>
                        <div className={`row mb-4`}>
                            <div className={`col-md-12`}>
                                <Select
                                    placeholder={`Välj skicklighet`}
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
                                    initialValue={ratingValueForMoney}
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
                                    initialValue={ratingRelationWithCustomer}
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
                                    initialValue={ratingQualityOfWork}
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
                                    initialValue={ratingPerformance}
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
                                <textarea className="form-control" id="ads_description" rows="6" value={feedBackDescription} onChange={handleFeedBackDescription} placeholder={`Skriv din feedback här`}>{feedBackDescription}</textarea>
                                {errors?.review && <p className="error-message">{errors.review[0]}</p>}
                            </div>
                        </div>
                        {(successMessage) && ( <div className={`col-md-12`}>
                            {successMessage && (
                                <Alert variant="success" className="" dismissible>
                                    <FaCheck className="me-2" /> {successMessage}
                                </Alert>
                            )}
                        </div>)}
                        <div className={`user-froms-btns d-flex justify-content-center`}>
                            <div className={`col-md-6`}>
                                <button type="submit" className="save-button outline-primary">Create</button>
                            </div>
                        </div>
                    </Form>
                </Modal.Body>
            </Modal>
            {/*Create Feedback Modal End*/}
        </>
    );
}