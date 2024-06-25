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
import { TagsInput } from "react-tag-input-component";
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

export default function EditSkill({ userDetails, userSkills, skillId }) {
    const [show, setShow] = useState(false);
    const {AuthToken} = useAppContext()
    const [editSkill, setEditSkill] = useState([])
    const [skillOptions, setSkillOptions] = useState([])
    const [selectedSkill, setSelectedSkill] = useState(null);
    const [selectedSkillName, setSelectedSkillName] = useState(null);
    const [errors, setErrors] = useState(null)
    const [mainErrors, setMainErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [isLoading, setIsLoading] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);
    const [skillDescription, setSkillDescription] = useState(null);
    const [tags, setTags] = useState([]);
    const [selectedPriceOption, setSelectedPriceOption] = useState('day'); // Default "Per Dag"
    const [pricePerDay, setPricePerDay] = useState(null); // State for price per day
    const [pricePerHour, setPricePerHour] = useState(null); // State for price per hour
    const [isDiscounted, setIsDiscounted] = useState(false);
    const [discountRate, setDiscountRate] = useState(null);
    const [discountDescription, setDiscountDescription] = useState(null);
    const [startDate, setStartDate] = useState(null);
    const [endDate, setEndDate] = useState(null);
    const [isActive, setIsActive] = useState(false);
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
        const formdata = {
            'skill_id':skillId,
            'uid': userDetails.id
        }
        axios.get(getPublicUrl + '/getSkills', config).then(response => {
            setSkillOptions(response.data.results)
        }).catch(e => {
            setErrors([...errors, e])
        })
    }

    async function getEditSkill(e) {
        e.preventDefault();

        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`}
        }
        const formdata = {
            'skill_id':skillId,
            'uid': userDetails.id
        }
        await axios.post(getPublicUrl + '/api/edit-spskills', formdata, config).then(response => {
            const editSkillTags = response.data.spskilldata?.tags.split(',');
            console.log('SKill Data : ', response.data.spskilldata);

            if( response.data.spskilldata ) {
                // Set Skill Pricing
                if( ( response.data.spskilldata.price_per_day !== '0.00' && response.data.spskilldata.price_per_hour !== '0.00' ) ) {
                    setSelectedPriceOption('both')
                    setPricePerDay(response.data.spskilldata.price_per_day)
                    setPricePerHour(response.data.spskilldata.price_per_hour)
                }else if( response.data.spskilldata?.price_per_day !== '0.00' || response.data.spskilldata?.price_per_hour === '0.00' ) {
                    setSelectedPriceOption('day')
                    setPricePerDay(response.data.spskilldata.price_per_day)
                }else if( response.data.spskilldata?.price_per_hour !== '0.00' || response.data.spskilldata?.price_per_day === '0.00' ) {
                    setSelectedPriceOption('hour')
                    setPricePerHour(response.data.spskilldata.price_per_hour)
                }

                // Set Skill Discount
                if( response.data.spskilldata?.offer_discount !== '' && response.data.spskilldata?.offer_discount !== null ){
                    setIsDiscounted(true)

                    if( response.data.spskilldata?.offer_discount ) {
                        setDiscountRate(response.data.spskilldata?.offer_discount)
                    }

                    if( response.data.spskilldata?.offer_desc && response.data.spskilldata.offer_desc !== '' ) {
                        setDiscountDescription(response.data.spskilldata.offer_desc)
                    }

                    if( response.data.spskilldata?.offer_start_date && response.data.spskilldata?.offer_end_date ) {
                        setStartDate(response.data.spskilldata.offer_start_date)
                        setEndDate(response.data.spskilldata.offer_end_date)
                    }
                }
            }

            setEditSkill(response.data.spskilldata)
            setSelectedSkill(response.data.spskilldata?.skill_id);
            setSkillDescription(response.data.spskilldata?.description)
            getSkillName(response.data.spskilldata?.skill_id)
            setTags(editSkillTags)
            setMap(prevMap => ({
                ...prevMap,
                lat: response.data.spskilldata?.latitude,
            }))
            setMap(prevMap => ({
                ...prevMap,
                lng: response.data.spskilldata?.longitudes,
            }))
            setMap(prevMap => ({
                ...prevMap,
                fulladdress: response.data.spskilldata?.address,
            }))
            setMap(prevMap => ({
                ...prevMap,
                locality: response.data.spskilldata?.city,
            }))
            setMap(prevMap => ({
                ...prevMap,
                administrative_area_level_1: response.data.spskilldata?.state,
            }))
            setMap(prevMap => ({
                ...prevMap,
                country: response.data.spskilldata?.country,
            }))
            setMap(prevMap => ({
                ...prevMap,
                postal_code: response.data.spskilldata?.pincode,
            }))
        }).catch(e => {
            setErrors([...errors, e])
        })
    }

    function getSkillName(skillId) {
        if( skillId ) {
            const matchingSkill = skillOptions.find((skill) => skill.id === skillId);
            setSelectedSkillName(matchingSkill.name)
        } else {
            setSelectedSkillName(null)
        }
    }

    const handleTextAreaChange = (event) => {
        setSkillDescription(event.target.value);
    };

    const handleTags = regularTags => {
        setTags(regularTags);
    };

    const handlePriceOptionChange = (event) => {
        const selectedVal = event.target.value;
        console.log('Field : ', selectedVal);
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

    const handleDiscountRate = (event) => {
        setDiscountRate(event.target.value);
    };

    const handleDiscountDescription = (event) => {
        setDiscountDescription(event.target.value);
    };

    const handleChangeStartDate = (event) => {
        if(!event.target.value) {
            setEndDate(null);
        }
        setStartDate(event.target.value);
    };

    const handleChangeEndDate = (event) => {
        setEndDate(event.target.value);
    };

    const handleMinEndDate = (startDate) => {
        if (startDate) {
            const newDate = new Date(startDate);
            newDate.setDate(newDate.getDate() + 1);
            return newDate.toISOString().slice(0, 10);
        }
        return null;
    };

    async function handleSkillChange(e) {
        const selectedValue = e.target.value;
        const selectedText = e.target.options[e.target.selectedIndex].textContent;
        setSelectedSkill(e.target.value);
        setSelectedSkillName(selectedText);
    }

    const handlePriceDiscount = (e) => {
        if( e.target.checked ) {
            setIsDiscounted(true)
            setDiscountRate(editSkill.offer_discount)
            setDiscountDescription(editSkill.offer_desc)
            setStartDate(editSkill.offer_start_date)
            setEndDate(editSkill.offer_end_date)
        } else {
            setIsDiscounted(false)
            setDiscountRate(null)
            setDiscountDescription(null)
            setStartDate(null)
            setEndDate(null)
        }
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

    const handleNextClick = () => {
        if (!selectedSkill) {
            setShowError(true);
            return;
        }
    };

    async function handleUpdate(e) {
        e.preventDefault();
        const formdata = {
            'uid': userDetails.id,
            'skillId':skillId,
            'skill_id':selectedSkill,
            'description':skillDescription,
            'tags':tags,
            'show_price':selectedPriceOption,
            'price_per_day':pricePerDay,
            'price_per_hour':pricePerHour,
            'skill_discount':isDiscounted,
            'offer_discount':discountRate,
            'offer_desc':discountDescription,
            'offer_start_date':startDate,
            'offer_end_date':endDate,
            'city':map.locality,
            'state':map.administrative_area_level_1,
            'country':map.country,
            'pincode':map.postal_code,
            'latitude':map.lat,
            'longitudes':map.lng,
            'address':map.fulladdress,
        }
        console.log('Prepared Update Skill Data : ', formdata);
        const config = {
            headers: { Authorization: `Bearer ${AuthToken}`}
        };

        await axios.post(getPublicUrl+'/api/update-spskills', formdata, config).then(response => {
            if( response.data.status == 200 ) {
                setIsLoading(false);
                setIsSuccess(true);
                setSuccessMessage(response.data.message);
                // Set interval for page refresh
                setTimeout(() => {
                  window.location.reload(); // Reload the page after 1000 milliseconds
                }, 1000);
            } else if( response.data.status == 403 ) {
                setIsLoading(false);
                setMainErrors(response.data.errors)
                console.log('UserInfo modal form submit errors: ', response.data.errors);
            }
        }).catch(error => {
            console.log('Form data : ', error);
            // setIsLoading(false);
            if (error.response && error.response.data && error.response.data.errors) {
                setMainErrors(error.response.data.errors)
            //     setErrors(error.response.data.errors);
            } else if( error.response && error.response.data && error.response.data.message ) {
                setMainErrors(error.response.data.message+' '+error.message)
            //     setErrors('An error occurred. Please try again.');
            }
        })
    }

    return (
        <>
            <button type={`button`} className="edit-button" data-bs-toggle="modal" data-bs-target={`#editSkillModal-${skillId}`} onClick={(e) => getEditSkill(e)}>
                <img src={`${getPublicUrl}/images/add-icon.png`} alt="edit-skill" />
            </button>

            {/*Skill Select Modal*/}
            <div className="modal fade" id={`editSkillModal-${skillId}`} tabIndex="-1" aria-labelledby={`editSkillModalLabel-${skillId}`} aria-hidden="true">
                <div className="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            {/*<h1 className="modal-title fs-5" id="exampleModalLabel">Modal title</h1>*/}
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            { errors && (<Alert variant="danger" className="mt-3" dismissible>
                                <FaCircleXmark className="me-2" /> {errors}
                            </Alert>) }
                            <h5 className={`skill-create-text mb-4`}>Grymt, du är på god väg att bli en helpii!!</h5>
                            <Form>
                                <label htmlFor="user-skill" className="form-label">Kompetens</label>
                                <select className="form-select user-skill" value={selectedSkill} onChange={(e) => handleSkillChange(e)}>
                                    <option key="" value="">Select Skill</option>
                                    {skillOptions.map((skill) => (
                                        <option key={skill.id || skill.value} value={skill.id || skill.value}>
                                            {skill.name || skill.label}
                                        </option>
                                    ))}
                                </select>
                                <div className={`user-availability-btns mt-3 d-flex justify-content-center gap-5`}>
                                    {/*<button type="submit" className="save-button outline-primary" disabled={isLoading || !selectedSkill || !errors}>{isLoading ? 'Confirming...' : 'Next'}</button>*/}
                                    <button type="button" className="save-button outline-primary" data-bs-target={`#editDescriptionModal-${skillId}`} data-bs-toggle="modal" disabled={!selectedSkill}>Next</button>
                                </div>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>

            {/*Skill Description Modal*/}
            <div className="modal fade" id={`editDescriptionModal-${skillId}`} tabIndex="-1" aria-labelledby={`editDescriptionModalLabel-${skillId}`} aria-hidden="true">
                <div className="modal-dialog modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            {/*<h1 className="modal-title fs-5" id="skill-description-modal">Modal 2</h1>*/}
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <Form>
                                <div className="mb-3">
                                    <div className={`d-flex justify-content-center gap-2 mb-4`}><h5>Talang:</h5> <h5 className={`user-skill-name`}>{selectedSkillName}</h5></div>
                                    <label htmlFor="skill-description" className="form-label">Beskrivning</label>
                                    <textarea className="form-control" id="skill-description" rows="3" value={skillDescription} onChange={handleTextAreaChange}></textarea>
                                </div>
                                <div className={`user-availability-btns mt-3 d-flex justify-content-center gap-5`}>
                                    <button type="button" className="save-button outline-primary" data-bs-target={`#editSkillModal-${skillId}`} data-bs-toggle="modal">Previous</button>
                                    <button type="button" className="save-button outline-primary" data-bs-target={`#editTagsModal-${skillId}`} data-bs-toggle="modal" disabled={!skillDescription}>Next</button>
                                </div>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>

            {/*Skill tags Modal*/}
            <div className="modal fade" id={`editTagsModal-${skillId}`} tabIndex="-1" aria-labelledby={`editTagsModalLabel-${skillId}`} aria-hidden="true">
                <div className="modal-dialog modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <Form>
                                <div className="mb-3">
                                    <div className={`d-flex justify-content-center gap-2`}><h5>Talang:</h5> <h5 className={`user-skill-name`}>{selectedSkillName}</h5></div>
                                    <div className={`skill-tags-text text-center`}>Här kan du skriva taggar för att skapa färdigheter, så att kunderna kan hitta din färdighet. (Ex: Skjut, bil, kör, med tryck på enter-tangenten så skapas taggar)</div>
                                    <TagsInput className={`user-skill-tags`} value={tags} onChange={setTags} name="skill-tags" placeHolder="Skriv taggar"/>
                                </div>
                                <div className={`user-availability-btns mt-3 d-flex justify-content-center gap-5`}>
                                    {/*<button type="submit" className="save-button outline-primary" disabled={isLoading || !selectedSkill || !errors}>{isLoading ? 'Confirming...' : 'Next'}</button>*/}
                                    <button type="button" className="save-button outline-primary" data-bs-target={`#editDescriptionModal-${skillId}`} data-bs-toggle="modal">Previous</button>
                                    <button type="button" className="save-button outline-primary" data-bs-target={`#editSkillPriceModal-${skillId}`} data-bs-toggle="modal" disabled={!tags.length}>Next</button>
                                </div>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>

            {/*Skill Pricing Modal*/}
            <div className="modal fade" id={`editSkillPriceModal-${skillId}`} aria-hidden="true" aria-labelledby={`editSkillPriceModalLabel-${skillId}`} tabIndex="-1">
                <div className="modal-dialog modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <Form>
                                <div className="mb-3">
                                    <div className={`d-flex justify-content-center gap-2`}><h5>Talang:</h5> <h5 className={`user-skill-name`}>{selectedSkillName}</h5></div>
                                </div>
                                <div className={`skill-price-text`}>Välj hur du vill få betalt. Per dag eller timme. Eller båda!</div>
                                <div className={`row ${selectedPriceOption === 'both' ? 'align-items-center' : ''} mt-4`}>
                                    <div className={`col-md-6`}>
                                        {selectedPriceOption === 'day' && (
                                            <>
                                                <label htmlFor="exampleFormControlInput1" className="form-label">Ditt pris</label>
                                                <input type="number" className="form-control" id="skill-price-per-day" placeholder="Pris Per Dag" value={pricePerDay} onChange={(e) => handlePriceChange(e, 'perDay')}/>
                                            </>
                                        )}
                                        {selectedPriceOption === 'hour' && (
                                            <>
                                                <label htmlFor="exampleFormControlInput1" className="form-label">Ditt pris</label>
                                                <input type="number" className="form-control" id="skill-price-per-hour" placeholder="Pris Per Timme" value={pricePerHour} onChange={(e) => handlePriceChange(e, 'perHour')}/>
                                            </>
                                        )}
                                        {selectedPriceOption === 'both' && (
                                            <>
                                                <div className={`skill-price-both`}>
                                                    <label htmlFor="exampleFormControlInput1" className="form-label">Ditt pris</label>
                                                    <input type="number" className="form-control mb-4" id="skill-price-per-day" placeholder="Pris Per Dag" value={pricePerDay} onChange={(e) => handlePriceChange(e, 'perDay')}/>
                                                    <label htmlFor="exampleFormControlInput1" className="form-label">Ditt pris</label>
                                                    <input type="number" className="form-control" id="skill-price-per-hour" placeholder="Pris Per Timme" value={pricePerHour} onChange={(e) => handlePriceChange(e, 'perHour')}/>
                                                </div>
                                            </>
                                        )}
                                    </div>
                                    <div className={`col-md-6`}>
                                        <label htmlFor="exampleFormControlInput1" className="form-label">Tid</label>
                                        <select className="form-select form-select-md mb-3" aria-label=".form-select-lg example" value={selectedPriceOption} onChange={handlePriceOptionChange}>
                                          <option value="day">Per Dag</option>
                                          <option value="hour">Per Timme</option>
                                          <option value="both">Både</option>
                                        </select>
                                    </div>
                                    <div className={`col-md-12 mt-4`}>
                                        <div className="form-check">
                                            {/*<input className="form-check-input" type="checkbox" value="" id="skill-price-discount" checked={isDiscounted} onChange={(event) => setIsDiscounted(event.target.checked)}/>*/}
                                            <input className="form-check-input" type="checkbox" value="" id="skill-price-discount" checked={isDiscounted} onChange={(e) => handlePriceDiscount(e)}/>
                                            <label className="form-check-label" htmlFor="skill-price-discount">Lägg till rabatt</label>
                                        </div>
                                    </div>
                                </div>
                                <div className={`user-availability-btns mt-3 d-flex justify-content-center gap-5`}>
                                    {/*<button type="submit" className="save-button outline-primary" disabled={isLoading || !selectedSkill || !errors}>{isLoading ? 'Confirming...' : 'Next'}</button>*/}
                                    <button type="button" className="save-button outline-primary" data-bs-target={`#editTagsModal-${skillId}`} data-bs-toggle="modal">Previous</button>
                                    <button type="button" className="save-button outline-primary" data-bs-target={isDiscounted ? `#editSkillDiscountModal-${skillId}` : `#editSkillAddressModal-${skillId}`} data-bs-toggle="modal">Next</button>
                                </div>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>

            {/*Skill Discount Modal*/}
            <div className="modal fade" id={`editSkillDiscountModal-${skillId}`} aria-hidden="true" aria-labelledby={`editSkillDiscountModalLabel-${skillId}`}>
                <div className="modal-dialog modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <Form>
                                <div className="mb-3">
                                    <div className={`d-flex justify-content-center gap-2`}><h5>Talang:</h5> <h5 className={`user-skill-name`}>{selectedSkillName}</h5></div>
                                </div>
                                <div className={`skill-discount-text`}>Erbjud eventuellt dina kunder rabatt till dina kunder.</div>
                                <div className={`row mt-4`}>
                                    <div className="col-md-12 mb-4">
                                        <label htmlFor="skill-discount" className="form-label">Rabatt (i %)</label>
                                        <input type="number" className="form-control " id="skill-discount" placeholder="Ange din rabatt" value={discountRate} onChange={handleDiscountRate}/>
                                    </div>
                                    <div className="col-md-12 mb-4">
                                        <label htmlFor="skill-descount-description" className="form-label">Erbjudandebeskrivning</label>
                                        <textarea className="form-control" id="skill-descount-description" rows="3" onChange={handleDiscountDescription} value={discountDescription} placeholder={`Rabattbeskrivning`}></textarea>
                                    </div>
                                    <div className="col-md-6 mb-4">
                                        <label htmlFor="skill-discount-startdate" className="form-label">Erbjudandets startdatum</label>
                                        <input type="date" className="form-control" id="skill-discount-startdate" placeholder="Ange din rabatt" value={startDate} onChange={handleChangeStartDate}/>
                                    </div>
                                    <div className="col-md-6 mb-4">
                                        <label htmlFor="skill-discount-enddate" className="form-label">Erbjudandets slutdatum</label>
                                        <input type="date" className="form-control" id="skill-discount-enddate" placeholder="Ange din rabatt" min={handleMinEndDate(startDate)} disabled={!startDate} onChange={handleChangeEndDate} value={endDate}/>
                                    </div>
                                </div>
                                <div className={`user-availability-btns mt-3 d-flex justify-content-center gap-5`}>
                                    {/*<button type="submit" className="save-button outline-primary" disabled={isLoading || !selectedSkill || !errors}>{isLoading ? 'Confirming...' : 'Next'}</button>*/}
                                    <button type="button" className="save-button outline-primary" data-bs-target={`#editSkillPriceModal-${skillId}`} data-bs-toggle="modal">Previous</button>
                                    <button type="button" className="save-button outline-primary" data-bs-target={`#editSkillAddressModal-${skillId}`} data-bs-toggle="modal">Next</button>
                                </div>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>

            {/*Skill Address Modal*/}
            <div className="modal fade" id={`editSkillAddressModal-${skillId}`} aria-hidden="true" aria-labelledby={`editSkillAddressModal-${skillId}`}>
                <div className="modal-dialog modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <Form onSubmit={handleUpdate}>
                                <div className="mb-3">
                                    <div className={`d-flex justify-content-center gap-2`}><h5>Talang:</h5> <h5 className={`user-skill-name`}>{selectedSkillName}</h5></div>
                                </div>
                                <div className={`skill-address-text`}>Välj hur du vill få betalt. Per dag eller timme. Eller båda!</div>
                                <div className={`row mt-4`}>
                                    <div className={`col-md-12 mt-4`}>
                                        <div htmlFor="skill-address" className="form-label">Från vilken stad utför du dina tjänster?</div>
                                        <div className="search-location-input mb-4">

                                            <label htmlFor="skill-address" className="form-label">City</label>
                                            <Autocomplete placeholder='Search city' className={`form-control mb-2`}
                                                apiKey='AIzaSyANGQiDmKPOHX5H5fUJQiuVsjhsL1Q3MtU'
                                                onPlaceSelected={(place) => {
                                                    addressCallBackHandler(place);
                                                }}
                                                value={map.locality} onChange={handleCityChange}
                                            />

                                            {map.locality && (
                                                <>
                                                    <div className="address-details mb-4">
                                                        <label htmlFor="skill-address" className="form-label">Zip code *</label>
                                                        <input type="text" className="form-control mb-2" placeholder="Zip code" value={map.postal_code} onChange={handleZipChange}/>
                                                        <label htmlFor="skill-address" className="form-label">Country</label>
                                                        <input type="text" className="form-control" placeholder="country" value={map.country} disabled/>
                                                    </div>
                                                </>
                                            )}
                                        </div>
                                        {/*<div className="form-check">
                                            <input className="form-check-input" type="checkbox" value="" id="skill-price-discount" checked={isActive} onChange={(event) => setIsActive(event.target.checked)}/>
                                            <label className="form-check-label" htmlFor="skill-price-discount">Aktivera</label>
                                        </div>*/}
                                    </div>
                                </div>
                                <div className={`row mt-3`}>
                                    <div className={`col-md-12`}>
                                        { successMessage && (<Alert variant="success" className="mb-4" dismissible>
                                            <FaCheck className="me-2" /> {successMessage}
                                        </Alert>) }
                                        { mainErrors && (<Alert variant="danger" className="mb-4" dismissible>
                                            <FaCircleXmark className="me-2" /> {mainErrors}
                                        </Alert>) }
                                    </div>
                                </div>
                                <div className={`user-availability-btns d-flex justify-content-center gap-5`}>
                                    <button type="button" className="save-button outline-primary" data-bs-target={isDiscounted ? `#editSkillDiscountModal-${skillId}` : `#editSkillPriceModal-${skillId}`} data-bs-toggle="modal">Previous</button>
                                    <button type="submit" className="save-button outline-primary" disabled={!map.locality}>Submit</button>
                                </div>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}