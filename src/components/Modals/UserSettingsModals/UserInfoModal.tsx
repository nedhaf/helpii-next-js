'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
import InputError from '@/components/InputError'
// import axios from 'axios'
import axios from '@/lib/axios'
import { Container, Row, Button, Col, Modal, Form, Alert, Card } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import {useAppContext} from '@/context'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function UserInfoModal({ userId, userProfile }) {
    const {Authuser, setAuthuser, AuthToken} = useAppContext()
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [isLoading, setIsLoading] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);
    const [FirstName, setFirstName] = useState(false);
    const [LastName, setLastName] = useState(false);
    const [Email, setEmail] = useState(false);
    const [Mobile, setMobile] = useState(false);
    const [LinkedIn, setLinkedIn] = useState(false);
    const [Insta, setInsta] = useState(false);
    const [Facebook, setFacebook] = useState(false);

    useEffect(() => {
        setFirstName(userProfile.first_name);
        setLastName(userProfile.last_name);
        setEmail(userProfile.email);
        setMobile(userProfile.profile?.phone || '');
        setLinkedIn(userProfile.profile?.linkedin || '');
        setInsta(userProfile.profile?.instagram || '');
        setFacebook(userProfile.profile?.facebook || '');
    }, []);


    async function handleUpdate(e) {
        e.preventDefault();
        setIsLoading(true);
        const formdatas = {
            'FirstName':FirstName,
            'LastName':LastName,
            'Mobile':Mobile,
            'LinkedIn':LinkedIn,
            'Insta':Insta,
            'Facebook':Facebook,
        }
        axios.post(getPublicUrl+'/api/user-details-update', formdatas).then(response => {
            // console.log('UserInfo modal form submit res: ', response);
            if( response.data.status == 200 ) {
                setIsLoading(false);
                setIsSuccess(true);
                setSuccessMessage(response.data.message);
                setAuthuser({ 'user': response.data.user})
            } else if( response.data.status == 403 ) {
                setIsLoading(false);
                console.log('UserInfo modal form submit errors: ', response.data);
                // setErrors(response.data.errors.country[0]);
            }
        }).catch(error => {
            setIsLoading(false);
            if (error.response && error.response.data && error.response.data.errors) {
                setErrors(error.response.data.errors);
            } else {
                setErrors('An error occurred. Please try again.');
            }
        })
    }

    return (
        <>
            <div className={`user-availability`}>
                <img src={`${getPublicUrl}/images/helpii-user-settings/user-info.svg`} alt="User Profile" />
                <span className={`user-settings-nm ms-4`}>Uppdatera profilinformation</span>
            </div>
            <div className={`edit-setting`} data-bs-toggle="modal" data-bs-target={`#userInfo-${userId}`}>
                <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
            </div>

            <div className="modal fade" id={`userInfo-${userId}`} tabIndex="-1" aria-labelledby={`userInfo-${userId}`} aria-hidden="true">
                <div className="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h1 className="modal-title fs-5" id="">Uppdatera profilinformation</h1>
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            { errors && (<Alert variant="danger" className="mt-3" dismissible>
                                <FaCircleXmark className="me-2" /> {errors}
                            </Alert>) }
                            <Form className={`user-details-form`} onSubmit={handleUpdate}>
                                <div className={`row`}>
                                    <div className={`col-md-12 mb-3`}>
                                        <label for="first_name" className="form-label"><strong>First Name</strong></label>
                                        <input type="text" className="form-control" id="first_name" placeholder="Jhone" value={FirstName} onChange={(e) => setFirstName(e.target.value)}/>
                                    </div>
                                    <div className={`col-md-12 mb-3`}>
                                        <label for="first_name" className="form-label"><strong>Last Name</strong></label>
                                        <input type="text" className="form-control" id="last_name" placeholder="Doy" value={LastName} onChange={(e) => setLastName(e.target.value)}/>
                                    </div>
                                    <div className={`col-md-12 mb-3`}>
                                        <label for="first_name" className="form-label"><strong>Mobile number</strong></label>
                                        <input type="text" className="form-control" id="mobile_no" placeholder="1234567890" value={Mobile} onChange={(e) => setMobile(e.target.value)}/>
                                    </div>
                                    <div className={`col-md-12 mb-3`}>
                                        <label for="first_name" className="form-label"><strong>Email</strong></label>
                                        <input type="email" className="form-control" id="email" placeholder="jhondoy@mail.com" disabled value={Email}/>
                                    </div>
                                </div>
                                <div className={`row mt-3`}>
                                    <h5 className={`form-heading`}>Social links</h5>
                                </div>
                                <div className={`row mb-3`}>
                                    <label for="linkdin_link" className="col-sm-1 col-form-label">
                                        <img className={`user-social-links-img`} src={`${getPublicUrl}/images/helpii-user-settings/linkedin-professional.svg`} alt="Delete Profile" />
                                    </label>
                                    <div className="col-sm-11">
                                      <input type="text" className="form-control" id="linkdin_link" placeholder="linkedin.com/your profile" value={LinkedIn} onChange={(e) => setLinkedIn(e.target.value)}/>
                                    </div>
                                </div>
                                <div className={`row mb-3`}>
                                    <label for="linkdin_link" className="col-sm-1 col-form-label">
                                        <img className={`user-social-links-img`} src={`${getPublicUrl}/images/helpii-user-settings/facebook-social-media.svg`} alt="Delete Profile" />
                                    </label>
                                    <div className="col-sm-11">
                                        <input type="text" className="form-control" id="linkdin_link" placeholder="linkedin.com/your profile" value={Facebook} onChange={(e) => setFacebook(e.target.value)}/>
                                    </div>
                                </div>
                                <div className={`row mb-3`}>
                                    <label for="linkdin_link" className="col-sm-1 col-form-label">
                                        <img className={`user-social-links-img`} src={`${getPublicUrl}/images/helpii-user-settings/rss-symbol.svg`} alt="Delete Profile" />
                                    </label>
                                    <div className="col-sm-11">
                                        <input type="text" className="form-control" id="linkdin_link" placeholder="instagram.com/your profile" value={Insta} onChange={(e) => setInsta(e.target.value)}/>
                                    </div>
                                </div>
                                {(successMessage) && ( <div className={`col-md-12 mt-4`}>
                                    {successMessage && (
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <FaCheck className="me-2" /> {successMessage}
                                        </div>
                                    )}
                                </div>)}
                                <div className={`user-info-btns mt-4 d-flex justify-content-center`}>
                                    <button type="submit" className="save-button outline-primary" disabled={isLoading}>{isLoading ? 'Updating...' : 'Update'}</button>
                                </div>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}