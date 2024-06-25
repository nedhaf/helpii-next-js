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

export default function UserCurrencyModal({ userId, userProfile }) {
    const [show, setShow] = useState(false);
    const {AuthToken} = useAppContext()
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [isSuccess, setIsSuccess] = useState(null);
    const [closeModal, setClose] = useState(false);
    const [currenciesOptions, setCurrenciesOptions] = useState(null);
    const [selectedCurrency, setSelectedCurrency] = useState(null);
    const [currencyId, setCurrencyId] = useState(null);
    const [currencyName, setCurrencyName] = useState(null);
    const [updatedCurrencyName, setUpdatedCurrencyName] = useState(null);

    useEffect(() => {
        allCurrencies()
        userCurrencies()
    }, []);

    // Get all currencies
    const allCurrencies = () => {
        const config = {
            headers: {
                'content-type': 'application/json',
            },
        }

        axios.get(getPublicUrl + '/api/get-currency', config).then(response => {
            if( response.data.results ) {
                setCurrenciesOptions(response.data.results)
            } else {
                setErrors(response.data.message)
            }
        }).catch(e => {
            console.log('Errors : ', e);
            setErrors([...errors, e])
        })
    }

    // Get User Currency

    const userCurrencies = () => {
        const config = {
            headers: {
                'content-type': 'application/json',
            },
        }

        const formdata = {
            'currency_id':userProfile?.currency_id,
        }

        axios.post(getPublicUrl + '/api/get-currency-by-id', formdata, config).then(response => {
            // console.log('User Currncy : ', response.data.results);
            setSelectedCurrency(response.data.results?.id)
            setCurrencyId(response.data.results?.id)
            setUpdatedCurrencyName(response.data.results?.symbol)
        }).catch(e => {
            console.log('Errors : ', e);
            setErrors([...errors, e])
        })
    }

    // async function getCurrency(e) {
    //     console.log('Get Currency : ', userProfile.currency_id);
    //     setSelectedCurrency(userProfile?.currency_id);
    // }

    async function handleCurrencyChange(e) {
        const selectedValue = e.target.value;
        const selectedText = e.target.options[e.target.selectedIndex].textContent;
        setCurrencyId(e.target.value);
        setCurrencyName(selectedText);
        setSelectedCurrency(selectedValue);
    }

    async function handleUpdateCurrency(e) {
        e.preventDefault();
        const formdata = {
            'currency_id':currencyId,
        }
        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`}
        };

        await axios.post(getPublicUrl+'/api/user-currency-update', formdata, config).then(response => {
            console.log('Currency from data : ', response.data.currency.symbol);
            setSuccessMessage(response.data.message)
            setUpdatedCurrencyName(response.data.currency.symbol)
            setTimeout(function () {
                // document.getElementById(`userCurrency-${userId}`).classList.remove("show", "d-block");
                // document.querySelectorAll(".modal-backdrop").forEach(el => el.classList.remove("modal-backdrop"));
                setSuccessMessage(null)
            }, 1500);
        }).catch(error => {
            if (error.response && error.response.data && error.response.data.errors) {
                setErrors(error.response.data.errors);
            } else {
                setErrors('An error occurred. Please try again.');
            }
        })
    }

    return (
        <>
            <div className={`user-currency`}>
                <img src={`${getPublicUrl}/images/helpii-user-settings/user-currency.svg`} alt="Currency" />
                <span className={`user-settings-nm ms-4`}>{updatedCurrencyName}</span>
            </div>
            <div className={`edit-setting`} data-bs-toggle="modal" data-bs-target={`#userCurrency-${userId}`}>
                <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
            </div>
            <div className="modal fade" id={`userCurrency-${userId}`} tabIndex="-1" aria-labelledby={`userCurrency-${userId}`} aria-hidden="true">
                <div className="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h1 className="modal-title fs-5" id="exampleModalLabel">Uppdatera Min Valuta</h1>
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <Form onSubmit={handleUpdateCurrency}>
                                <div className={`row`}>
                                    <div className={`col-md-12 mb-3`}>
                                        <select className="form-select user-currency" onChange={(e) => handleCurrencyChange(e)} value={selectedCurrency}>
                                            {currenciesOptions && currenciesOptions.map((currency) => (
                                                <option key={currency.id || currency.value} value={currency.id || currency.value}>
                                                    {currency.symbol}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
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