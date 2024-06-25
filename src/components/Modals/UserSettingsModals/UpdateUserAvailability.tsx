'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
// import axios from 'axios'
import axios from '@/lib/axios'
import { Container, Row, Button, Col, Modal, Form, Alert, Card } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import Select from 'react-select';

type TypeCountries = {
   value: string,
   label: string | ReactElement,
   flag: string,
}

const { Option } = Select;

type PickerType = 'time' | 'date';

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function UpdateUserAvailability({ showAvailabilitySettingModal, onAvailabilitySettingClose, userId, userProfile }) {
    const [isLangSettingOpen, setLangSettingIsOpen] = useState(showAvailabilitySettingModal);
    const [selectedOption, setSelectedOption] = useState(null);
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [availabilityData, setAvailabilityData] = useState({ availability: '' });
    const [isLoading, setIsLoading] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);

    useEffect(() => {
        setLangSettingIsOpen(showAvailabilitySettingModal);
    }, [showAvailabilitySettingModal]);

    const handleClose = () => {
        setLangSettingIsOpen(false);
        onAvailabilitySettingClose && onAvailabilitySettingClose();
    };

    async function handleSubmit(e) {
        e.preventDefault();
        setIsLoading(true);
    }

    return (
        <>
            <Modal className={`user-edit-availability-modal user-edit-availability-modal-${userId}`} centered backdrop="static" size="md" show={isLangSettingOpen} onHide={handleClose}>
                <Modal.Header closeButton>
                    <h2>Uppdatera Min Tillgänglighet</h2>
                </Modal.Header>
                <Modal.Body>
                    { successMessage && (<Alert variant="success" className="mt-3" dismissible>
                        <FaCheck className="me-2" /> {successMessage}
                    </Alert>) }
                    { errors && (<Alert variant="danger" className="mt-3" dismissible>
                        <FaCircleXmark className="me-2" /> {errors}
                    </Alert>) }
                    <Form onSubmit={handleSubmit}>
                        <div className="card card-availability mb-4">
                            <div className="card-body">
                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                    <h5 className="card-title">Monday</h5>
                                    <Form.Check reverse type="switch" id="availability-switche-mon" label="Close" className="availability-switche"/>
                                </div>
                                <Row className={`availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                    <Col md={2} className={`text-center`}>
                                        TO
                                    </Col>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                </Row>
                            </div>
                        </div>

                        <div className="card card-availability mb-4">
                            <div className="card-body">
                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                    <h5 className="card-title">Tuesday</h5>
                                    <Form.Check reverse type="switch" id="availability-switche-tue" label="Close" className="availability-switche"/>
                                </div>
                                <Row className={`availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                    <Col md={2} className={`text-center`}>
                                        TO
                                    </Col>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                </Row>
                            </div>
                        </div>

                        <div className="card card-availability mb-4">
                            <div className="card-body">
                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                    <h5 className="card-title">Wednesday</h5>
                                    <Form.Check reverse type="switch" id="availability-switche-wed" label="Close" className="availability-switche"/>
                                </div>
                                <Row className={`availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                    <Col md={2} className={`text-center`}>
                                        TO
                                    </Col>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                </Row>
                            </div>
                        </div>

                        <div className="card card-availability mb-4">
                            <div className="card-body">
                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                    <h5 className="card-title">Thursday</h5>
                                    <Form.Check reverse type="switch" id="availability-switche-thu" label="Close" className="availability-switche"/>
                                </div>
                                <Row className={`availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                    <Col md={2} className={`text-center`}>
                                        TO
                                    </Col>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                </Row>
                            </div>
                        </div>

                        <div className="card card-availability mb-4">
                            <div className="card-body">
                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                    <h5 className="card-title">Friday</h5>
                                    <Form.Check reverse type="switch" id="availability-switche-fri" label="Close" className="availability-switche"/>
                                </div>
                                <Row className={`availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                    <Col md={2} className={`text-center`}>
                                        TO
                                    </Col>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                </Row>
                            </div>
                        </div>

                        <div className="card card-availability mb-4">
                            <div className="card-body">
                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                    <h5 className="card-title">Saturday</h5>
                                    <Form.Check reverse type="switch" id="availability-switche-sat" label="Close"
                                        className="availability-switche"/>
                                </div>
                                <Row className={`availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                    <Col md={2} className={`text-center`}>
                                        TO
                                    </Col>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                </Row>
                            </div>
                        </div>

                        <div className="card card-availability mb-4">
                            <div className="card-body">
                                <div className={`d-flex align-items-center justify-content-between`}>
                                    <h5 className="card-title">Sunday</h5>
                                    <Form.Check reverse type="switch" id="availability-switche-sun" label="Close"
                                        className="availability-switche"/>
                                </div>
                                <Row className={`availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                    <Col md={2} className={`text-center`}>
                                        TO
                                    </Col>
                                    <Col md={5}>
                                        <input type="time" class="form-control" value="10:05 AM" />
                                    </Col>
                                </Row>
                            </div>
                        </div>

                        <div className={`user-availability-btns mt-3 d-flex justify-content-center`}>
                            <button type="submit" className="save-button outline-primary" disabled={isLoading}>{isLoading ? 'Confirming...' : 'Confirm'}</button>
                        </div>
                    </Form>
                </Modal.Body>
            </Modal>
        </>
    );
}