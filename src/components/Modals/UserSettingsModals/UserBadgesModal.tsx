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

export default function UserBadgesModal({ userId, userProfile }) {
    const {AuthToken} = useAppContext()
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [isSuccess, setIsSuccess] = useState(null);
    const [helpiiBadges, setHelpiiBadges] = useState(null);
    const [helpiiSelectedBadges, setHelpiielectedBadges] = useState(null);
    const [selectedBadgeId, setSelectedBadgeId] = useState(null);

    useEffect(() => {
        if (userProfile && userProfile.badge_id != null ) {
            const defaultBadgeId = userProfile.badge_id;
            setSelectedBadgeId(defaultBadgeId);
            setHelpiielectedBadges(defaultBadgeId);
        }
    }, [userProfile]);

    // Get all badges
    const allBadges = () => {
        const config = {
            headers: {
                'content-type': 'application/json',
            },
        }

        axios.get(getPublicUrl + '/api/get-badges', config).then(response => {
            if( response.data.results ) {
                setHelpiiBadges(response.data.results)
            } else {
                setErrors(response.data.message)
            }
        }).catch(e => {
            console.log('Errors : ', e);
            setErrors([...errors, e])
        })
    }

    const onChooseBadge = (event) => {
        setHelpiielectedBadges(parseInt(event.target.value))
    }

    // Update Badge
    async function handleUpdateBadge(e) {
        e.preventDefault();
        const formdata = {
            'uid':userId,
            'ub_id':helpiiSelectedBadges,
            'type':'add'
        };

        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`}
        };

        await axios.post(getPublicUrl+'/api/update-user-badges', formdata, config).then(response => {
            setSuccessMessage(response.data.message)
            setTimeout(function () {
                // document.getElementById(`userCurrency-${userId}`).classList.remove("show", "d-block");
                // document.querySelectorAll(".modal-backdrop").forEach(el => el.classList.remove("modal-backdrop"));
                setSuccessMessage(null)
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

    // console.log('Default Badge : ', selectedBadgeId)
    // console.log('Selected Badge : ', helpiiSelectedBadges)
    return (
        <>
            <div className={`user-badge`}>
                <img src={`${getPublicUrl}/images/helpii-user-settings/user-badge.svg`} alt="Currency" />
                <span className={`user-settings-nm ms-4`}>Jag fakturerar med FF</span>
            </div>
            <div className={`edit-setting`} data-bs-toggle="modal" data-bs-target={`#userBadge-${userId}`}>
                <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" onClick={allBadges}/>
            </div>

            <div className="modal fade" id={`userBadge-${userId}`} tabIndex="-1" aria-labelledby={`userBadge-${userId}`} aria-hidden="true">
                <div className="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h1 className="modal-title fs-5" id="exampleModalLabel">Uppdatera Märke</h1>
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <Form onSubmit={handleUpdateBadge}>
                                <div className={`row user-primary-badges`}>
                                    {helpiiBadges && helpiiBadges.map((badgePrimary, indx) => (
                                        badgePrimary.id === 2 || badgePrimary.id === 3 || badgePrimary.id === 4 ? (
                                            <>
                                                <div className={`col-md-4 mb-3`} key={indx}>
                                                    <input type="radio" className="btn-check" name="user-primary-badge" id={`user-badge-${badgePrimary.id}`} autocomplete="off" value={badgePrimary.id} onChange={onChooseBadge} checked={helpiiSelectedBadges === badgePrimary.id ? true : false}/>
                                                    <label className="btn justify-content-center align-items-center" htmlFor={`user-badge-${badgePrimary.id}`}>
                                                        <img className={`user-badges-img me-2`} src={`${getPublicUrl}/storage/badges/${badgePrimary.img}`} alt={badgePrimary.badge_name}/>{badgePrimary.badge_name}
                                                    </label>
                                                </div>
                                            </>
                                        ) : ''
                                    ))}
                                </div>
                                <div className={`row user-secondary-badges`}>
                                    {helpiiBadges && helpiiBadges.map((badgeSecondary, indx) => (
                                        badgeSecondary.id !== 2 && badgeSecondary.id !== 3 && badgeSecondary.id !== 4 ? (
                                            <>
                                                <div className={`col-md-4 mb-3`} key={indx}>
                                                    <input type="radio" className="btn-check" name="user-primary-badge" id={`user-badge-${badgeSecondary.id}`} autocomplete="off" value={badgeSecondary.id} onChange={onChooseBadge} checked={helpiiSelectedBadges === badgeSecondary.id ? true : false}/>
                                                    <label className="btn justify-content-center align-items-center" htmlFor={`user-badge-${badgeSecondary.id}`}>
                                                        <img className={`user-badges-img me-2`} src={`${getPublicUrl}/storage/badges/${badgeSecondary.img}`} alt={badgeSecondary.badge_name}/>{badgeSecondary.badge_name}
                                                    </label>
                                                </div>
                                            </>
                                        ) : ''
                                    ))}
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