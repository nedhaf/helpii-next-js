'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
import InputError from '@/components/InputError'
// import axios from 'axios'
import axios from '@/lib/axios'
import { Container, Row, Button, Col, Modal, Form, Alert, Card } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function UpdateInfo({ openUpdateInfoModal, onUpdateInfoClose, userId, userProfile }) {
    const [isUpdateInfoOpen, setUpdateInfoIsOpen] = useState(openUpdateInfoModal);
    const [errors, setErrors] = useState([])
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
        setUpdateInfoIsOpen(openUpdateInfoModal);
        setFirstName(userProfile.first_name);
        setLastName(userProfile.last_name);
        setEmail(userProfile.email);
        setMobile(userProfile.profile.phone || '');
        setLinkedIn(userProfile.profile.linkedin || '');
        setInsta(userProfile.profile.instagram || '');
        setFacebook(userProfile.profile.facebook || '');
    }, [openUpdateInfoModal]);

    const handleClose = () => {
        setUpdateInfoIsOpen(false);
        onUpdateInfoClose && onUpdateInfoClose();
    };

    async function handleSubmit(e) {
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
            console.log('UserInfo modal form submit res: ', response);
            if( response.data.status == 200 ) {
                setIsLoading(false);
                setIsSuccess(true);
                setSuccessMessage(response.data.msg);
                setTimeout(function () {
                    // document.getElementById(`userCurrency-${userId}`).classList.remove("show", "d-block");
                    // document.querySelectorAll(".modal-backdrop").forEach(el => el.classList.remove("modal-backdrop"));
                    setSuccessMessage(null)
                }, 1500);
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
            <Modal className={`user-edit-info-modal user-edit-info-modal-${userId}`} centered backdrop="static" size="md" show={isUpdateInfoOpen} onHide={handleClose}>
                <Modal.Header closeButton>
                    <h2>Uppdatera profilinformation</h2>
                </Modal.Header>
                <Modal.Body>
                    { successMessage && (<Alert variant="success" className="mt-3" dismissible>
                        <FaCheck className="me-2" /> {successMessage}
                    </Alert>) }
                    { errors && (<Alert variant="danger" className="mt-3" dismissible>
                        <FaCircleXmark className="me-2" /> {errors}
                    </Alert>) }
                    <Form onSubmit={handleSubmit}>
                        <div className={`user-details-form mb-3`}>
                            <label for="first_name" className="form-label">First Name</label>
                            <input type="text" className="form-control" id="first_name" placeholder="Jhone" value={FirstName} onChange={(e) => setFirstName(e.target.value)}/>
                        </div>
                        <div className={`user-details-form mb-3`}>
                            <label for="last_name" className="form-label">Last Name</label>
                            <input type="text" className="form-control" id="last_name" placeholder="Doy" value={LastName} onChange={(e) => setLastName(e.target.value)}/>
                        </div>
                        <div className={`user-details-form mb-3`}>
                            <label for="mobile_no" className="form-label">Mobile number</label>
                            <input type="text" className="form-control" id="mobile_no" placeholder="1234567890" value={Mobile} onChange={(e) => setMobile(e.target.value)}/>
                            <InputError messages={errors.Mobile} className="mt-2" />
                        </div>
                        <div className={`user-details-form mb-3`}>
                            <label for="email" className="form-label">Email</label>
                            <input type="email" className="form-control" id="email" placeholder="jhondoy@mail.com" disabled value={Email}/>
                        </div>
                        <h5>Social links</h5>
                        <div className="mb-3 row">
                            <label for="linkdin_link" className="col-sm-1 col-form-label">
                                <img className={`user-social-links-img`} src={`${getPublicUrl}/images/helpii-user-settings/linkedin-professional.svg`} alt="Delete Profile" />
                            </label>
                            <div className="col-sm-11">
                              <input type="text" className="form-control" id="linkdin_link" placeholder="linkedin.com/your profile" value={LinkedIn} onChange={(e) => setLinkedIn(e.target.value)}/>
                            </div>
                        </div>
                        <div className="mb-3 row">
                            <label for="linkdin_link" className="col-sm-1 col-form-label">
                                <img className={`user-social-links-img`} src={`${getPublicUrl}/images/helpii-user-settings/facebook-social-media.svg`} alt="Delete Profile" />
                            </label>
                            <div className="col-sm-11">
                              <input type="text" className="form-control" id="linkdin_link" placeholder="linkedin.com/your profile" value={Facebook} onChange={(e) => setFacebook(e.target.value)}/>
                            </div>
                        </div>
                        <div className="mb-3 row">
                            <label for="linkdin_link" className="col-sm-1 col-form-label">
                                <img className={`user-social-links-img`} src={`${getPublicUrl}/images/helpii-user-settings/rss-symbol.svg`} alt="Delete Profile" />
                            </label>
                            <div className="col-sm-11">
                              <input type="text" className="form-control" id="linkdin_link" placeholder="linkedin.com/your profile" value={Insta} onChange={(e) => setInsta(e.target.value)}/>
                            </div>
                        </div>
                        <div className={`user-info-btns mt-3 d-flex justify-content-center`}>
                            <button type="submit" className="save-button outline-primary" disabled={isLoading}>{isLoading ? 'Updating...' : 'Update'}</button>
                        </div>
                    </Form>
                </Modal.Body>
            </Modal>
        </>
    );
}