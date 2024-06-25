'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
// import axios from 'axios'
import axios from '@/lib/axios'
import { Container, Row, Button, Col, Modal, Form, Alert, Card } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import {useAppContext} from '@/context'
import CustomDatePicker from "@/components/CommonComponents/CustomTimePicker";
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import { TimeClock } from '@mui/x-date-pickers/TimeClock';

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function UserAvailabilityModal({ userId, userProfile }) {
    const {AuthToken} = useAppContext()
    const [show, setShow] = useState(false);
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [isSuccess, setIsSuccess] = useState(false);
    const [startTimeMon, setStartTimeMon] = useState(null);
    const [endTimeMon, setEndTimeMon] = useState(null);
    const [closeAvailabilityMon, setCloseAvailabilityMon] = useState(0);
    const [startTimeTue, setStartTimeTue] = useState(null);
    const [endTimeTue, setEndTimeTue] = useState(null);
    const [closeAvailabilityTue, setCloseAvailabilityTue] = useState(0);
    const [startTimeWed, setStartTimeWed] = useState(null);
    const [endTimeWed, setEndTimeWed] = useState(null);
    const [closeAvailabilityWed, setCloseAvailabilityWed] = useState(0);
    const [startTimeThu, setStartTimeThu] = useState(null);
    const [endTimeThu, setEndTimeThu] = useState(null);
    const [closeAvailabilityThu, setCloseAvailabilityThu] = useState(0);
    const [startTimeFri, setStartTimeFri] = useState(null);
    const [endTimeFri, setEndTimeFri] = useState(null);
    const [closeAvailabilityFri, setCloseAvailabilityFri] = useState(0);
    const [startTimeSat, setStartTimeSat] = useState(null);
    const [endTimeSat, setEndTimeSat] = useState(null);
    const [closeAvailabilitySat, setCloseAvailabilitySat] = useState(0);
    const [startTimeSun, setStartTimeSun] = useState(null);
    const [endTimeSun, setEndTimeSun] = useState(null);
    const [closeAvailabilitySun, setCloseAvailabilitySun] = useState(0);

    async function getAvailability(e) {
        const formdata = {
            "uid":userId
        }

        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`}
        };

        await axios.post(getPublicUrl+'/api/get-user-availability', formdata, config).then(response => {
            if( response.data.timeslots ) {
                // monday
                setStartTimeMon(response.data.timeslots.monday.from)
                setEndTimeMon(response.data.timeslots.monday.to)
                setCloseAvailabilityMon(response.data.timeslots.monday.close)

                // tuesday
                setStartTimeTue(response.data.timeslots.tuesday.from)
                setEndTimeTue(response.data.timeslots.tuesday.to)
                setCloseAvailabilityTue(response.data.timeslots.tuesday.close)

                // wednesday
                setStartTimeWed(response.data.timeslots.wednesday.from)
                setEndTimeWed(response.data.timeslots.wednesday.to)
                setCloseAvailabilityWed(response.data.timeslots.wednesday.close)

                // thursday
                setStartTimeThu(response.data.timeslots.thursday.from)
                setEndTimeThu(response.data.timeslots.thursday.to)
                setCloseAvailabilityThu(response.data.timeslots.thursday.close)

                // friday
                setStartTimeFri(response.data.timeslots.friday.from)
                setEndTimeFri(response.data.timeslots.friday.to)
                setCloseAvailabilityFri(response.data.timeslots.friday.close)

                // saturday
                setStartTimeSat(response.data.timeslots.saturday.from)
                setEndTimeSat(response.data.timeslots.saturday.to)
                setCloseAvailabilitySat(response.data.timeslots.saturday.close)

                // sunday
                setStartTimeSun(response.data.timeslots.sunday.from)
                setEndTimeSun(response.data.timeslots.sunday.to)
                setCloseAvailabilitySun(response.data.timeslots.sunday.close)
            }
        }).catch(error => {
            if (error.response && error.response.data && error.response.data.errors) {
                setErrors(error.response.data.errors);
            } else {
                setErrors('An error occurred. Please try again.');
            }
        })
    }

    const handleCloseAvailability = (event, day) => {
        const isChecked = event.target.checked;

        switch (day) {
          case "mon":
            setCloseAvailabilityMon(isChecked ? 1 : 0)
            // if( isChecked ) {
            //     setStartTimeMon(null)
            //     setEndTimeMon(null)
            // } else {
            //     setStartTimeMon(startTimeMon || null)
            //     setEndTimeMon(endTimeMon || null)
            // }
            break;
        case "tue":
            setCloseAvailabilityTue(isChecked ? 1 : 0)
            // if( isChecked ) {
            //     setStartTimeTue(null)
            //     setEndTimeTue(null)
            // } else {
            //     setStartTimeTue(startTimeTue || null)
            //     setEndTimeTue(endTimeTue || null)
            // }
            break;
        case "wed":
            setCloseAvailabilityWed(isChecked ? 1 : 0)
            // if( isChecked ) {
            //     setStartTimeWed(null)
            //     setEndTimeWed(null)
            // } else {
            //     setStartTimeWed(startTimeWed || null)
            //     setEndTimeWed(endTimeWed || null)
            // }
            break;
        case "thu":
            setCloseAvailabilityThu(isChecked ? 1 : 0)
            // if( isChecked ) {
            //     setStartTimeThu(null)
            //     setEndTimeThu(null)
            // } else {
            //     setStartTimeThu(startTimeThu || null)
            //     setEndTimeThu(endTimeThu || null)
            // }
            break;
        case "fri":
            setCloseAvailabilityFri(isChecked ? 1 : 0)
            // if( isChecked ) {
            //     setStartTimeFri(null)
            //     setEndTimeFri(null)
            // } else {
            //     setStartTimeFri(startTimeFri || null)
            //     setEndTimeFri(endTimeFri || null)
            // }
            break;
        case "sat":
            setCloseAvailabilitySat(isChecked ? 1 : 0)
            // if( isChecked ) {
            //     setStartTimeSat(null)
            //     setEndTimeSat(null)
            // } else {
            //     setStartTimeSat(startTimeSat || null)
            //     setEndTimeSat(endTimeSat || null)
            // }
            break;
        case "sun":
            setCloseAvailabilitySun(isChecked ? 1 : 0)
            // if( setCloseAvailabilitySun === 1 ) {
            //     setStartTimeSun(null)
            //     setEndTimeSun(null)
            // } else {
            //     setStartTimeSun(startTimeSun || null)
            //     setEndTimeSun(endTimeSun || null)
            // }
            break;
          default:
            break;
        }
    };

    const handleStartTimeChange = (event, day) => {
        switch (day) {
          case "mon":
            setStartTimeMon(event.target.value)
            break;
        case "tue":
            setStartTimeTue(event.target.value)
            break;
        case "wed":
            setStartTimeWed(event.target.value)
            break;
        case "thu":
            setStartTimeThu(event.target.value)
            break;
        case "fri":
            setStartTimeFri(event.target.value)
            break;
        case "sat":
            setStartTimeSat(event.target.value)
            break;
        case "sun":
            setStartTimeSun(event.target.value)
            break;
          default:
            break;
        }
    };

    const handleEndTimeChange = (event, day) => {
        switch (day) {
          case "mon":
            setEndTimeMon(event.target.value)
            break;
        case "tue":
            setEndTimeTue(event.target.value)
            break;
        case "wed":
            setEndTimeWed(event.target.value)
            break;
        case "thu":
            setEndTimeThu(event.target.value)
            break;
        case "fri":
            setEndTimeFri(event.target.value)
            break;
        case "sat":
            setEndTimeSat(event.target.value)
            break;
        case "sun":
            setEndTimeSun(event.target.value)
            break;
          default:
            break;
        }
    };

    // Update Availability
    async function handleUpdate(e) {
        e.preventDefault();
        const formdata = {
            'monday':{
                'from':startTimeMon,
                'to':endTimeMon,
                'close':closeAvailabilityMon,
            },
            'tuesday':{
                'from':startTimeTue,
                'to':endTimeTue,
                'close':closeAvailabilityTue,
            },
            'wednesday':{
                'from':startTimeWed,
                'to':endTimeWed,
                'close':closeAvailabilityWed,
            },
            'thursday':{
                'from':startTimeThu,
                'to':endTimeThu,
                'close':closeAvailabilityThu,
            },
            'friday':{
                'from':startTimeFri,
                'to':endTimeFri,
                'close':closeAvailabilityFri,
            },
            'saturday':{
                'from':startTimeSat,
                'to':endTimeSat,
                'close':closeAvailabilitySat,
            },
            'sunday':{
                'from':startTimeSun,
                'to':endTimeSun,
                'close':closeAvailabilitySun,
            }
        }

        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`}
        };

        await axios.post(getPublicUrl+'/api/availability-create-update', formdata, config).then(response => {

             setSuccessMessage(response.data.message)
            // setUpdatedCurrencyName(response.data.currency.symbol)
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
            <div className={`user-availability`}>
                <img src={`${getPublicUrl}/images/helpii-user-settings/user-availability.svg`} alt="Delete Profile" />
                <span className={`user-settings-nm ms-4`}>Uppdatera min tillgänglighet</span>
            </div>
            <div className={`edit-setting`} data-bs-toggle="modal" data-bs-target={`#userAvailability-${userId}`} onClick={(e) => getAvailability(e)}>
                <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
            </div>
            <div className="modal fade" id={`userAvailability-${userId}`} tabIndex="-1" aria-labelledby={`userAvailability-${userId}`} aria-hidden="true">
                <div className="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h1 className="modal-title fs-5" id="exampleModalLabel">Uppdatera Min Tillgänglighet</h1>
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            { errors && (<Alert variant="danger" className="mt-3" dismissible>
                                <FaCircleXmark className="me-2" /> {errors}
                            </Alert>) }
                            <Form onSubmit={handleUpdate}>
                                {/*Monday Avilability Time Card*/}
                                <div className={`card card-availability-monday mb-3`}>
                                    <div className="card-body">
                                        <div className={`row`}>
                                            <div className={`col-md-12`}>
                                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                                    <h5 className="card-title">Monday</h5>
                                                    <div className="availability-switche form-check form-check-reverse form-switch">
                                                        <label className="form-check-label me-2" htmlFor="availability-switche-mon">Close</label>
                                                        <input className="form-check-input" type="checkbox" role="switch" id="availability-switche-mon" onChange={(e) =>handleCloseAvailability(e, "mon")} checked={closeAvailabilityMon === 1}/>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div className={`row availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={startTimeMon} id={`availability-mon-start-time`} onChange={(e) =>handleStartTimeChange(e, "mon")}/>
                                            </div>
                                            <div className={`col-md-2 text-center`}>TO</div>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={endTimeMon} id={`availability-mon-end-time`} onChange={(e) => handleEndTimeChange(e, "mon")}/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/*Tuesday Avilability Time Card*/}
                                <div className={`card card-availability-tuesday mb-3`}>
                                    <div className="card-body">
                                        <div className={`row`}>
                                            <div className={`col-md-12`}>
                                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                                    <h5 className="card-title">Tuesday</h5>
                                                    <div className="availability-switche form-check form-check-reverse form-switch">
                                                        <label className="form-check-label me-2" htmlFor="availability-switche-tue">Close</label>
                                                        <input className="form-check-input" type="checkbox" role="switch" id="availability-switche-tue" onChange={(e) =>handleCloseAvailability(e, "tue")} checked={closeAvailabilityTue === 1}/>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div className={`row availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={startTimeTue} id={`availability-tue-start-time`} onChange={(e) =>handleStartTimeChange(e, "tue")}/>
                                            </div>
                                            <div className={`col-md-2 text-center`}>TO</div>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={endTimeTue} id={`availability-tue-end-time`} onChange={(e) => handleEndTimeChange(e, "tue")}/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/*Wednesday Avilability Time Card*/}
                                <div className={`card card-availability-wednesday mb-3`}>
                                    <div className="card-body">
                                        <div className={`row`}>
                                            <div className={`col-md-12`}>
                                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                                    <h5 className="card-title">Wednesday</h5>
                                                    <div className="availability-switche form-check form-check-reverse form-switch">
                                                        <label className="form-check-label me-2" htmlFor="availability-switche-wed">Close</label>
                                                        <input className="form-check-input" type="checkbox" role="switch" id="availability-switche-wed" onChange={(e) =>handleCloseAvailability(e, "wed")} checked={closeAvailabilityWed === 1}/>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div className={`row availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={startTimeWed} id={`availability-wed-start-time`} onChange={(e) =>handleStartTimeChange(e, "wed")}/>
                                            </div>
                                            <div className={`col-md-2 text-center`}>TO</div>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={endTimeWed} id={`availability-wed-end-time`} onChange={(e) => handleEndTimeChange(e, "wed")}/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/*Thursday Avilability Time Card*/}
                                <div className={`card card-availability-thursday mb-3`}>
                                    <div className="card-body">
                                        <div className={`row`}>
                                            <div className={`col-md-12`}>
                                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                                    <h5 className="card-title">Thursday</h5>
                                                    <div className="availability-switche form-check form-check-reverse form-switch">
                                                        <label className="form-check-label me-2" htmlFor="availability-switche-thu">Close</label>
                                                        <input className="form-check-input" type="checkbox" role="switch" id="availability-switche-thu" onChange={(e) =>handleCloseAvailability(e, "thu")} checked={closeAvailabilityThu === 1}/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className={`row availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={startTimeThu} id={`availability-thu-start-time`} onChange={(e) =>handleStartTimeChange(e, "thu")}/>
                                            </div>
                                            <div className={`col-md-2 text-center`}>TO</div>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={endTimeThu} id={`availability-thu-end-time`} onChange={(e) => handleEndTimeChange(e, "thu")}/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/*Friday Avilability Time Card*/}
                                <div className={`card card-availability-friday mb-3`}>
                                    <div className="card-body">
                                        <div className={`row`}>
                                            <div className={`col-md-12`}>
                                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                                    <h5 className="card-title">Friday</h5>
                                                    <div className="availability-switche form-check form-check-reverse form-switch">
                                                        <label className="form-check-label me-2" htmlFor="availability-switche-fri">Close</label>
                                                        <input className="form-check-input" type="checkbox" role="switch" id="availability-switche-fri" onChange={(e) =>handleCloseAvailability(e, "fri")} checked={closeAvailabilityFri === 1}/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className={`row availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={startTimeFri} id={`availability-fri-start-time`} onChange={(e) =>handleStartTimeChange(e, "fri")}/>
                                            </div>
                                            <div className={`col-md-2 text-center`}>TO</div>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={endTimeFri} id={`availability-fri-end-time`} onChange={(e) => handleEndTimeChange(e, "fri")}/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/*Saturday Avilability Time Card*/}
                                <div className={`card card-availability-friday mb-3`}>
                                    <div className="card-body">
                                        <div className={`row`}>
                                            <div className={`col-md-12`}>
                                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                                    <h5 className="card-title">Saturday</h5>
                                                    <div className="availability-switche form-check form-check-reverse form-switch">
                                                        <label className="form-check-label me-2" htmlFor="availability-switche-sat">Close</label>
                                                        <input className="form-check-input" type="checkbox" role="switch" id="availability-switche-sat" onChange={(e) =>handleCloseAvailability(e, "sat")} checked={closeAvailabilitySat === 1}/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className={`row availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={startTimeSat} id={`availability-sat-start-time`} onChange={(e) =>handleStartTimeChange(e, "sat")}/>
                                            </div>
                                            <div className={`col-md-2 text-center`}>TO</div>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={endTimeSat} id={`availability-sat-end-time`} onChange={(e) => handleEndTimeChange(e, "sat")}/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/*Sunday Avilability Time Card*/}
                                <div className={`card card-availability-friday mb-3`}>
                                    <div className="card-body">
                                        <div className={`row`}>
                                            <div className={`col-md-12`}>
                                                <div className={`d-flex align-items-center justify-content-between mb-4`}>
                                                    <h5 className="card-title">Sunday</h5>
                                                    <div className="availability-switche form-check form-check-reverse form-switch">
                                                        <label className="form-check-label me-2" htmlFor="availability-switche-sun">Close</label>
                                                        <input className="form-check-input" type="checkbox" role="switch" id="availability-switche-sun" onChange={(e) =>handleCloseAvailability(e, "sun")} checked={closeAvailabilitySun === 1}/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className={`row availability-times-wrapper d-flex align-items-center justify-content-between`}>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={startTimeSun} id={`availability-sun-start-time`} onChange={(e) =>handleStartTimeChange(e, "sun")}/>
                                            </div>
                                            <div className={`col-md-2 text-center`}>TO</div>
                                            <div className={`col-md-5`}>
                                                <input type="time" class="form-control" value={endTimeSun} id={`availability-sun-end-time`} onChange={(e) => handleEndTimeChange(e, "sun")}/>
                                            </div>
                                        </div>
                                    </div>
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
}