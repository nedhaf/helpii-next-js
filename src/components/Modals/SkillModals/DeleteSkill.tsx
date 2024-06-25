'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
import axios from 'axios'
import { Container, Row, Button, Col, Modal, Form, Alert, Card } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import {useAppContext} from '@/context'
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.js';

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function DeleteSkill({ userDetails, skillId }) {
    const {AuthToken} = useAppContext()
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [isLoading, setIsLoading] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);

    async function deleteCurrentSkill(e, id) {
        e.preventDefault();
        console.log('Deleting skill id : ', id);
        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`}
        };
        const formdata = {
            'skill_id':id,
            'uid': userDetails.id
        }
        await axios.post(getPublicUrl+'/api/delete-spskills', formdata, config).then(response => {
            console.log('UserInfo modal form submit res: ', response);
            if( response.data.status == 200 ) {
                setIsLoading(false);
                setIsSuccess(true);
                setSuccessMessage(response.data.message);
                // Set interval for page refresh
                setTimeout(() => {
                  window.location.reload();
                }, 1000);
            } else if( response.data.status == 403 ) {
                setIsLoading(false);
                setErrors(response.data.errors[0])
                console.log('UserInfo modal form submit errors: ', response.data.errors);
            }
        }).catch(error => {
            console.log('Form data : ', error);
            // setIsLoading(false);
            if (error.response && error.response.data && error.response.data.errors) {
                setErrors(error.response.data.errors[0])
            //     setErrors(error.response.data.errors);
            } else if( error.response && error.response.data && error.response.data.message ) {
                setErrors(error.response.data.message+' '+error.message)
            //     setErrors('An error occurred. Please try again.');
            }
        })
    }
    return (
        <>
            <button type={`button`} className="delete-button" data-bs-toggle="modal" data-bs-target={`#deleteSkillModal-${skillId}`}>
                <img src={`${getPublicUrl}/images/delete-icon.png`} alt="delete-skill" />
            </button>

            <div className="modal fade" id={`deleteSkillModal-${skillId}`} tabIndex="-1" aria-labelledby={`deleteSkillModalLabel-${skillId}`} aria-hidden="true">
                <div className="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <div className={`row`}>
                                <div className={`col-md-12`}>
                                    <h5 className={'helpii-texts mb-4'}>Är du säker på att du vill ta bort denna färdighet?</h5>
                                        { successMessage && (<Alert variant="success" className="mb-4" dismissible>
                                            <FaCheck className="me-2" /> {successMessage}
                                        </Alert>) }
                                        { errors && (<Alert variant="danger" className="mb-4" dismissible>
                                            <FaCircleXmark className="me-2" /> {errors}
                                        </Alert>) }
                                    <div className={`d-flex justify-content-center gap-5`}>
                                        <button type="button" className="yes-button outline-primary" onClick={(e) => deleteCurrentSkill(e, skillId)}>Yes</button>
                                        <button type="button" className="no-button outline-primary" data-bs-dismiss="modal" aria-label="Close">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}