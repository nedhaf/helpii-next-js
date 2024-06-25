'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
// import axios from 'axios'
import axios from '@/lib/axios'
import { Container, Row, Button, Col, Modal, Form, Alert, Card } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import {useAppContext} from '@/context'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function UserUploadProfilePicModal({ userId, userProfile, checkAuth }) {
    const {AuthToken} = useAppContext()
    const [showModal, setShowModal] = useState(false)
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [avatarURL, setAvatarURL] = useState(getPublicUrl+'/'+userProfile.avatar_type+'/'+userProfile.avatar_location);
    const [originalAvatarURL, setoriginalAvatarURL] = useState(getPublicUrl+'/'+userProfile.avatar_type+'/'+userProfile.avatar_location);
    const [AvatarFile, setAvatarFile] = useState(null);
    const [uploadAvatarURL, setUploadAvatarURL] = useState('');
    const fileUploadRef = useRef()

    var myModalEl = document.getElementById(`userProfileImageModal-${userId}`)
    // var myModalEl = document.querySelector(`#userProfileImageModal-${userId}`)
    const modalBackdrops = document.getElementsByClassName('modal-backdrop');

    const openModal = () => {
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
    };

    const handleImageUpload = (event) => {
        event.preventDefault();
        fileUploadRef.current.click();
    };

    const uploadImageDisplay = async () => {
        const uploadedFile = fileUploadRef.current.files[0];
        const cachedURL = URL.createObjectURL(uploadedFile);
        setAvatarFile(event.target.files[0]);
        setAvatarURL(cachedURL);
        setUploadAvatarURL(fileUploadRef.current.files)
    }

    // function handleCloseModal(){
    //     // setAvatarURL(getPublicUrl+'/'+userProfile.avatar_type+'/'+userProfile.avatar_location);
    //     // document.getElementById(`userProfileImageModal-${userId}`).classList.remove("show", "d-block");
    //     // document.querySelectorAll(".modal-backdrop")
    //     //         .forEach(el => el.classList.remove("modal-backdrop"));
    // }

    // Update user profile image
    async function handleUpdateUserProfileImage(e) {
        e.preventDefault();
        const formData = new FormData();
        formData.append('uid', userId);
        formData.append('avtar_image', AvatarFile);
        formData.append('avatar_type', 'storage');
        formData.append('avatar_location', '');

        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`,"Content-Type": "multipart/form-data"}
        };

        await axios.post(getPublicUrl+'/api/upload-avtar', formData, config).then(response => {
            setSuccessMessage(response.data.message)

            setAvatarURL(getPublicUrl+'/'+response.data.location)
            setoriginalAvatarURL(getPublicUrl+'/'+response.data.location)
            setTimeout(function () {
                // document.getElementById(`userCurrency-${userId}`).classList.remove("show", "d-block");
                // document.querySelectorAll(".modal-backdrop").forEach(el => el.classList.remove("modal-backdrop"));
                setSuccessMessage(null)

                // myModalEl.classList.remove('show');
                // myModalEl.setAttribute('aria-hidden', 'true');
                // myModalEl.setAttribute('style', 'display: none');
                // document.body.removeChild(modalBackdrops[0]);
            }, 1500);
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
                // document.getElementById(`userCurrency-${userId}`).classList.remove("show", "d-block");
                // document.querySelectorAll(".modal-backdrop").forEach(el => el.classList.remove("modal-backdrop"));
                setErrors(null)
            }, 1500);
        })

    }
    return (
        <>
            <div className={`user-profile-img`}>
                <img className="profileimage" src={originalAvatarURL}  alt="profile-image"/>
                {checkAuth && checkAuth.id == userProfile.id && <>
                    <button type={`button`} className={`upload-profile-pic`} data-bs-toggle="modal" data-bs-target={`#userProfileImageModal-${userId}`} onClick={openModal}>
                        <img className="" src={getPublicUrl+'/images/camera.svg'}  alt="camera"/>
                    </button>
                </>}
            </div>

            {showModal &&(
                <>
                </>
            )}
            <div className="modal fade" id={`userProfileImageModal-${userId}`} tabIndex="-1" aria-labelledby={`userProfileImageModal-${userId}`} aria-hidden="true">
                <div className="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h1 className="modal-title fs-5" id="exampleModalLabel">Byt profilbild</h1>
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <Form onSubmit={handleUpdateUserProfileImage}>
                                <div className={`row`}>
                                    <div className={`col-md-12`}>
                                        <div className={`user-profile-img d-flex justify-content-center`}>
                                            <img className="profileimage" src={avatarURL}  alt="profile-image"/>
                                            <button type={`button`} className={`upload-profile-pic-modal`} onClick={handleImageUpload}>
                                                <img className="" src={getPublicUrl+'/images/camera.svg'}  alt="camera"/>
                                            </button>
                                            <input type="file" id="file" accept="image/*" ref={fileUploadRef} onChange={uploadImageDisplay} hidden />
                                        </div>
                                    </div>
                                    <div className={`col-md-12`}></div>
                                </div>
                                <div className={`row`}>
                                    <div className={`col-md-12`}>
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
                                        <div className={`user-froms-btns d-flex justify-content-center gap-4`}>
                                            <button type="button" className="no-button outline-primary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                            <button type="submit" className="save-button outline-primary">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}