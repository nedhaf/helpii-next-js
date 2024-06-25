'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
// import axios from '@/lib/axios'
import axios from 'axios'
import { Container, Row, Button, Col, Modal, Form, Alert, Card } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import Select from 'react-select';
import {useAppContext} from '@/context'
import { LoadScript, GoogleMap, Marker } from '@react-google-maps/api';
import Autocomplete from 'react-google-autocomplete';
import OverlayTrigger from 'react-bootstrap/OverlayTrigger';
import Popover from 'react-bootstrap/Popover';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.js';

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']
const libraries = ['places'];

const componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'long_name',
    country: 'long_name',
    postal_code: 'short_name'
};


export default function CreateAds({ userDetails, userAds }) {
    const {AuthToken} = useAppContext()

    const [isActive, setIsActive] = useState(false);
    const [skillOptions, setSkillOptions] = useState([])
    const [selectedSkill, setSelectedSkill] = useState(null);
    const [selectedSkillName, setSelectedSkillName] = useState(null);
    const [selectedPriceOption, setSelectedPriceOption] = useState('day'); // Default "Per Dag"
    const [pricePerDay, setPricePerDay] = useState(null); // State for price per day
    const [pricePerHour, setPricePerHour] = useState(null); // State for price per hour
    const [adsHeadline, setAdsHeadline] = useState('');
    const [adsDescription, setAdsDescription] = useState(null);
    const [errors, setErrors] = useState(null)
    const [mainErrors, setMainErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [isLoading, setIsLoading] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);
    const [map, setMap] = useState({
        street_number: '',
        route: '',
        locality: '', // city
        administrative_area_level_1: '', // state
        country: '',
        postal_code: '',
        lng: '',
        lat: '',
        fulladdress: '',
    });

    useEffect(() => {
        allSkills()
    }, []);

    // Get all skills
    const allSkills = () => {
        const config = {
            headers: {
                'content-type': 'application/json',
                'x-inertia': 'true',
            },
        }

        axios.get(getPublicUrl + '/getSkills', config).then(response => {
            setSkillOptions(response.data.results)
        }).catch(e => {
            setErrors([...errors, e])
        })
    }

    async function handleSkillChange(e) {
        const selectedValue = e.target.value;
        const selectedText = e.target.options[e.target.selectedIndex].textContent;
        setSelectedSkill(e.target.value);
        setSelectedSkillName(selectedText);
    }

    const addressCallBackHandler = (data) => {

        let formatted_address = data.formatted_address;
        let ac  = data.address_components;
        let lat = data.geometry.location.lat();
        let lon = data.geometry.location.lng();

        setMap(prevMap => ({
            ...prevMap,
            lat: lat,
        }))
        setMap(prevMap => ({
            ...prevMap,
            lng: lon,
        }))
        setMap(prevMap => ({
            ...prevMap,
            fulladdress: formatted_address,
        }))

        let address_fields;

        var city = "";
        var state = "";
        var country = "";

        for (var i = 0; i < ac.length; i++) {
            var addressType = ac[i].types[0];

            if(addressType == "postal_town"){
                var val = place.address_components[i]["long_name"];
            }

            if(addressType == "locality"){
                city = ac[i].long_name;
                setMap(prevMap => ({
                    ...prevMap,
                    locality: city,
                }))
            }else if (addressType === 'administrative_area_level_1') {
                state = ac[i].short_name;
                setMap(prevMap => ({
                    ...prevMap,
                    administrative_area_level_1: state,
                }))
            } else if (addressType === 'country') {
                country = ac[i].long_name;
                setMap(prevMap => ({
                    ...prevMap,
                    country: country,
                }))
            }
        }
    }

    const handleCityChange = (event) => {
        setMap((prevMap) => ({
            ...prevMap,
            locality: event.target.value,
        }));
    };

    const handleZipChange = (event) => {
        setMap((prevMap) => ({
            ...prevMap,
            postal_code: event.target.value,
        }));
    };

    const handlePriceOptionChange = (event) => {
        const selectedVal = event.target.value;
        if( selectedVal == 'day' ) {
            setPricePerHour('');
        } else if( selectedVal == 'hour' ) {
            setPricePerDay('');
        }
        setSelectedPriceOption(selectedVal);
    };

    const handlePriceChange = (event, field) => {
        const value = event.target.value;
        if (field === 'perDay') {
            setPricePerDay(value);
        } else if (field === 'perHour') {
            setPricePerHour(value);
        }
    };

    const handleHeadlineChange = (event) => {
        setAdsHeadline(event.target.value);
    };

    const handleTextAreaChange = (event) => {
        setAdsDescription(event.target.value);
    };

    async function handleSubmit(e) {
        e.preventDefault();
        const formdata = {
            'skill_id':selectedSkill,
            'city':map.locality,
            'state':map.administrative_area_level_1,
            'country':map.country,
            'pincode':map.postal_code,
            'latitude':map.lat,
            'longitudes':map.lng,
            'address':map.fulladdress,
            'title':adsHeadline,
            'show_price':selectedPriceOption,
            'price_per_day':pricePerDay,
            'price_per_hour':pricePerHour,
            'description':adsDescription,
        }
        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`}
        };

        // console.log('Ads from data : ', formdata);
        await axios.post(getPublicUrl+'/api/create-ad', formdata, config).then(response => {
            console.log('UserAds modal form submit res: ', response);
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
                setMainErrors(response.data.errors)
                console.log('UserAds modal form submit errors: ', response.data.errors);
            }
        }).catch(error => {
            console.log('Form data : ', error);
            if (error.response && error.response.data && error.response.data.errors) {
                setMainErrors(error.response.data.errors)
            } else if( error.response && error.response.data && error.response.data.message ) {
                setMainErrors(error.response.data.message+' '+error.message)
            }
        })
    }

    return (
        <>
            <button type={`button`} className="add-button" data-bs-toggle="modal" data-bs-target={`#adsModal`}>
                <img src={`${getPublicUrl}/images/plus-icon.png`} alt="create-ad" />Nytt
            </button>

            {/*Skill Select Modal*/}
            <div className="modal fade" id="adsModal" tabIndex="-1" aria-labelledby="adsModalLabel" aria-hidden="true">
                <div className="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h1 className="modal-title fs-5" id="exampleModalLabel">Skapa Annons</h1>
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            { errors && (<Alert variant="danger" className="mt-3" dismissible>
                                <FaCircleXmark className="me-2" /> {errors}
                            </Alert>) }
                            <Form onSubmit={handleSubmit}>
                                <div className={`row`}>
                                    <div className={`col-md-12 mb-3`}>
                                        <label htmlFor="user-skill" className="form-label">Kompetens</label>
                                        <select className="form-select user-skill" value={selectedSkill} onChange={(e) => handleSkillChange(e)}>
                                            <option key="" value="">Select Skill</option>
                                            {skillOptions.map((skill) => (
                                                <option key={skill.id || skill.value} value={skill.id || skill.value}>
                                                    {skill.name || skill.label}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className={`col-md-12 mb-3`}>
                                        <label htmlFor="skill-address" className="form-label">City</label>
                                        <Autocomplete placeholder='Search city' className={`form-control mb-2`}
                                            apiKey='AIzaSyANGQiDmKPOHX5H5fUJQiuVsjhsL1Q3MtU'
                                            onPlaceSelected={(place) => {
                                                addressCallBackHandler(place);
                                            }}
                                            value={map.locality} onChange={handleCityChange}
                                        />
                                        {(map.locality && map.country) && (
                                            <>
                                                <div className="address-details mb-3">
                                                    <label htmlFor="skill-address" className="form-label">Zip code *</label>
                                                    <input type="text" className="form-control mb-2" placeholder="Zip code" value={map.postal_code} onChange={handleZipChange}/>
                                                    <label htmlFor="skill-address" className="form-label">Country</label>
                                                    <input type="text" className="form-control" placeholder="country" value={map.country} disabled/>
                                                </div>
                                            </>
                                        )}
                                    </div>
                                    <div className={`col-md-12 mb-3`}>
                                        <label htmlFor="ads-headline" className="form-label">Headline</label>
                                        <input type="text" className="form-control mb-2" placeholder="Headline" value={adsHeadline} onChange={handleHeadlineChange}/>
                                    </div>
                                </div>
                                <div className={`row mb-3 ${selectedPriceOption === 'both' ? 'align-items-center' : ''}`}>
                                    <div className={`col-md-6`}>
                                        {selectedPriceOption === 'day' && (
                                            <>
                                                <label htmlFor="ads-price-per-day" className="form-label">Ditt pris</label>
                                                <input type="number" className="form-control" id="ads-price-per-day" placeholder="Pris Per Dag" value={pricePerDay} onChange={(e) => handlePriceChange(e, 'perDay')}/>
                                            </>
                                        )}
                                        {selectedPriceOption === 'hour' && (
                                            <>
                                                <label htmlFor="ads-price-per-hour" className="form-label">Ditt pris</label>
                                                <input type="number" className="form-control" id="ads-price-per-hour" placeholder="Pris Per Timme" value={pricePerHour} onChange={(e) => handlePriceChange(e, 'perHour')}/>
                                            </>
                                        )}
                                        {selectedPriceOption === 'both' && (
                                            <>
                                                <div className={`ads-price-both`}>
                                                    <label htmlFor="ads-price-per-day" className="form-label">Ditt pris</label>
                                                    <input type="number" className="form-control mb-4" id="ads-price-per-day" placeholder="Pris Per Dag" value={pricePerDay} onChange={(e) => handlePriceChange(e, 'perDay')}/>
                                                    <label htmlFor="ads-price-per-hour" className="form-label">Ditt pris</label>
                                                    <input type="number" className="form-control" id="ads-price-per-hour" placeholder="Pris Per Timme" value={pricePerHour} onChange={(e) => handlePriceChange(e, 'perHour')}/>
                                                </div>
                                            </>
                                        )}
                                    </div>
                                    <div className={`col-md-6`}>
                                        <label htmlFor="ads-price-type" className="form-label">Tid</label>
                                        <select className="form-select form-select-md" id={`ads-price-type`} aria-label=".form-select-lg example" value={selectedPriceOption} onChange={handlePriceOptionChange}>
                                          <option value="day">Per Dag</option>
                                          <option value="hour">Per Timme</option>
                                          <option value="both">Både</option>
                                        </select>
                                    </div>
                                </div>
                                <div className={`row`}>
                                    <div className={`col-md-12 mb-3`}>
                                        <label htmlFor="ads-description" className="form-label">Description</label>
                                        <textarea className="form-control" id="ads-description" rows="3" value={adsDescription} onChange={handleTextAreaChange} placeholder={`Description`}></textarea>
                                    </div>

                                    {(successMessage || mainErrors) && ( <div className={`col-md-12`}>
                                        {successMessage && (
                                            <Alert variant="success" className="" dismissible>
                                                <FaCheck className="me-2" /> {successMessage}
                                            </Alert>
                                        )}
                                        {mainErrors && (
                                            <Alert variant="danger" className="" dismissible>
                                                <FaCircleXmark className="me-2" /> {mainErrors}
                                            </Alert>
                                        )}
                                    </div>)}

                                    <div className={`user-froms-btns d-flex justify-content-center gap-4`}>
                                        <button type="button" className="no-button outline-primary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                        <button type="submit" className="save-button outline-primary">Submit</button>
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