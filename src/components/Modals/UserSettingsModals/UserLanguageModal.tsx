'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
// import axios from 'axios'
import axios from '@/lib/axios'
import { Container, Row, Button, Col, Modal, Form, Alert } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import Select from 'react-select';

type TypeCountries = {
   value: string,
   label: string | ReactElement,
   flag: string,
}

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function UserLanguageModal({ showLangSettingModal, onLangSettingClose, userId, userProfile }) {
    const [isLangSettingOpen, setLangSettingIsOpen] = useState(showLangSettingModal);
    const [selectedOption, setSelectedOption] = useState(null);
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [countryData, setCountryData] = useState({ country: '' });
    const [isLoading, setIsLoading] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);

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

    useEffect(() => {
        setLangSettingIsOpen(showLangSettingModal);
    }, [showLangSettingModal]);

    const handleClose = () => {
        setLangSettingIsOpen(false);
        onLangSettingClose && onLangSettingClose();
    };

    async function handleSubmit(e) {
        e.preventDefault();
        setIsLoading(true);

        axios.post(getPublicUrl+'/api/user-language-update', countryData).then(response => {
            if( response.data.status == 200 ) {
                setIsLoading(false);
                setIsSuccess(true);
                setSuccessMessage(response.data.msg);
            } else if( response.data.status == 403 ) {
                setIsLoading(false);
                setErrors(response.data.errors.country[0]);
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

    const selectedLanguage = userProfile ? countries.find((country) => country.value === userProfile.language) : null;
    return (
        <>
            <Modal className={`user-edit-language-modal user-edit-language-modal-${userId}`} centered backdrop="static" size="md" show={isLangSettingOpen} onHide={handleClose}>
                <Modal.Header closeButton>
                    <h2>Redigera språk</h2>
                </Modal.Header>
                <Modal.Body>
                    { successMessage && (<Alert variant="success" className="mt-3" dismissible>
                        <FaCheck className="me-2" /> {successMessage}
                    </Alert>) }
                    { errors && (<Alert variant="danger" className="mt-3" dismissible>
                        <FaCircleXmark className="me-2" /> {errors}
                    </Alert>) }
                    <Form onSubmit={handleSubmit}>
                        <div className='mb-10'>
                            <label className='form-label'>Välj ett land</label>
                            <Select className='react-select-styled' classNamePrefix='react-select'
                                options={countries.map((item) => {
                                    item.label = (
                                       <div className='label'>
                                          <img src={item.flag} alt={item.label} className='w-20px me-2' />
                                          <span>{item.label}</span>
                                       </div>
                                    )
                                    return item
                                })}
                                isSearchable={true}
                                placeholder='Välj ett land'
                                defaultValue={userProfile ? selectedLanguage : countries[0]}
                                isClearable
                                onChange={(selectedOption) =>
                                    {
                                        if (selectedOption) {
                                            setCountryData({ ...countryData, country: selectedOption.value })
                                        } else {
                                            setCountryData({ ...countryData, country: null })
                                        }
                                    }
                                }
                            />
                        </div>
                        {/*{isLoading && <p className="text-center mt-4">Loading...</p>}*/}
                        <div className={`user-language-btns mt-3 d-flex justify-content-center`}>
                            {/*<Button className={`me-2`} variant="secondary" onClick={handleClose}>Close</Button>*/}
                            {/*<Button type="submit" className="filtet-button" disabled={isLoading}>{isLoading ? 'Saving...' : 'Save'}</Button>*/}
                            <button type="submit" className="save-button outline-primary" disabled={isLoading}>{isLoading ? 'Saving...' : 'Save'}</button>
                        </div>
                    </Form>
                </Modal.Body>
            </Modal>
        </>
    );
}