'use client'
import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
import axios from 'axios'
import { Container, Row, Button, Col, Modal, Form } from 'react-bootstrap'
import { FaCheck } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import Autocomplete from 'react-google-autocomplete';
import {useAppContext} from '@/context'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function SearchModal(props) {
    const router = useRouter();
    const {appResults, setAppResults} = useAppContext()
    const [errors, setErrors] = useState(null)
    const [showModal, setShowModal] = useState(false)
    const [searchTypeRadio, setSearchTypeRadio] = useState('skills')
    const [skillOptions, setSkillOptions] = useState(null)
    const [badgeOptions, setBadgeOptions] = useState(null)
    const [selectedBadges, setSelectedBadges] = useState(null)
    const [selectedSkills, setSelectedSkills] = useState(null)
    const [selectedRatings, setSelectedRatings] = useState(null)
    const [search, setSearch] = useState({
        skillid: null,
        profile_badge: null,
        rating: null,
        street_number: null,
        route: null,
        locality: null, // city
        administrative_area_level_1: null, // state
        country: null,
        postal_code: null,
        fulladdress: null,
        lng: null,
        lat: null,
        search_for: 'skills',
    })

    // Define ratings
    const ratings = [1, 2, 3, 4, 5]
    useEffect(() => {
        // allSkills()
        // allBadges()
        // Retrieve stored data from localStorage
        const storedSearchType = JSON.parse(localStorage.getItem('searchType'))
        if (storedSearchType) {
            setSelectedSkills(storedSearchType)
            setSearch({
              ...search,
              search_for: storedSearchType,
            });
        }

        const storedSkills = JSON.parse(localStorage.getItem('skillIds'))
        if (storedSkills) {
            setSelectedSkills(storedSkills)
        }

        const storedBadges = JSON.parse(localStorage.getItem('badgeId'))
        if (storedBadges) {
            setSelectedBadges(storedBadges)
        }

        const storedRating = JSON.parse(localStorage.getItem('rating'))
        if (storedRating) {
            setSelectedRatings(storedRating)
        }
    }, [])

    // Get all skills
    const allSkills = () => {
        const config = {
            headers: {
                'content-type': 'application/json',
            },
        }

        axios.get(getPublicUrl + '/getSkills', config).then(response => {
            setSkillOptions(response.data.results)
        }).catch(e => {
            setErrors(e)
        })
    }

    // Get all badges
    const allBadges = () => {
        const config = {
            headers: {
                'content-type': 'application/json',
            },
        }

        axios.get(getPublicUrl + '/getBadges', config).then(response => {
            setBadgeOptions(response.data.results)
        }).catch(e => {
            setErrors(e)
        })
    }

    const handleShowModal = () => {
        allSkills()
        allBadges()
        setShowModal(true)
    }

    const handleCloseModal = () => {
        setShowModal(false)
    }

    // Search type
    const handleChangeSearchType = (searchType) => {
        setSearchTypeRadio(searchType)
        localStorage.setItem('searchType',JSON.stringify(searchType))
        setSearch({
          ...search,
          search_for: searchType,
        });
    }

    // Badge Selection
    const handleBadgeClick = (badgeId) => {
        setSelectedBadges(badgeId === selectedBadges ? null : badgeId);
        localStorage.setItem('badgeId',JSON.stringify(badgeId === selectedBadges ? null : badgeId))
        setSearch({
          ...search,
          profile_badge: badgeId === selectedBadges ? null : badgeId,
        });
    };

    // Badge Selection
    const handleSkillCheckboxChange = (skillId) => {
        setSelectedSkills(skillId === selectedSkills ? null : skillId);
        localStorage.setItem('skillIds',JSON.stringify(skillId === selectedSkills ? null : skillId))
        setSearch({
          ...search,
          skillid: skillId === selectedSkills ? null : skillId,
        });
    };

    // Rating Selection
    const handleRatingClick = (rating) => {
        setSelectedRatings(rating === selectedRatings ? null : rating);
        localStorage.setItem('rating',JSON.stringify(rating === selectedRatings ? null : rating))
        setSearch({
          ...search,
          rating: rating === selectedRatings ? null : rating,
        });
    };

    // Search by Address
    const addressCallBackHandler = (data) => {

        let formatted_address = data.formatted_address;
        let ac  = data.address_components;
        let lat = data.geometry.location.lat();
        let lon = data.geometry.location.lng();

        setSearch(prevMap => ({
            ...prevMap,
            lat: lat,
        }))
        setSearch(prevMap => ({
            ...prevMap,
            lng: lon,
        }))
        setSearch(prevMap => ({
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
                setSearch(prevMap => ({
                    ...prevMap,
                    locality: city,
                }))
            }else if (addressType === 'administrative_area_level_1') {
                state = ac[i].short_name;
                setSearch(prevMap => ({
                    ...prevMap,
                    administrative_area_level_1: state,
                }))
            } else if (addressType === 'country') {
                country = ac[i].long_name;
                setSearch(prevMap => ({
                    ...prevMap,
                    country: country,
                }))
            }
        }
    }

    const handleCityChange = (event) => {
        setSearch((prevMap) => ({
            ...prevMap,
            locality: event.target.value,
        }));
    };

    const handleZipChange = (event) => {
        setSearch((prevMap) => ({
            ...prevMap,
            postal_code: event.target.value,
        }));
    };

    // Search User Call
    const handleSearch = e => {
        e.preventDefault();
        axios.post(getPublicUrl+'/api/search-users', search).then(response => {
            const searchResults = response
            if( response.data.status == 200 ) {
                if( response.data.errors != '' ) {
                    setErrors(response.data.errors)
                } else {
                    setErrors(null)
                    setAppResults({ results: searchResults });
                    router.push('/filter-users')
                    setShowModal(false)
                }
            } else {
                if( response.data.errors || response.data.errors != null ) {
                    setErrors(response.data.errors)
                }
                // console.log('Search res Else : ', response);
            }

        })
    }

    // Reset Form
    const handleResetSearchForm = (e) => {
        e.preventDefault();

        setSearchTypeRadio('skills')
        setSelectedBadges(null)
        setSelectedSkills(null)
        setSelectedRatings(null)
        setSearch(prevMap => ({
            ...prevMap,
            lat: '',
        }))
        setSearch(prevMap => ({
            ...prevMap,
            lng: '',
        }))
        setSearch(prevMap => ({
            ...prevMap,
            fulladdress: '',
        }))
        setSearch(prevMap => ({
            ...prevMap,
            locality: '',
        }))
        setSearch(prevMap => ({
            ...prevMap,
            administrative_area_level_1: '',
        }))
        setSearch(prevMap => ({
            ...prevMap,
            country: '',
        }))
        setSearch((prevMap) => ({
            ...prevMap,
            locality: '',
        }));
        setSearch((prevMap) => ({
            ...prevMap,
            postal_code: '',
        }));

        localStorage.setItem('searchType',JSON.stringify('skills'))
        localStorage.setItem('badgeId',JSON.stringify(null))
        localStorage.setItem('skillIds',JSON.stringify(null))
        localStorage.setItem('rating',JSON.stringify(null))
    }

    // Cancle Form
    const handleCancelSearchForm = (e) => {
        e.preventDefault();
        setErrors(null)
        setShowModal(false)
        setSearchTypeRadio('skills')
        setSelectedBadges(null)
        setSelectedSkills(null)
        setSelectedRatings(null)
        setSearch(prevMap => ({
            ...prevMap,
            lat: '',
        }))
        setSearch(prevMap => ({
            ...prevMap,
            lng: '',
        }))
        setSearch(prevMap => ({
            ...prevMap,
            fulladdress: '',
        }))
        setSearch(prevMap => ({
            ...prevMap,
            locality: '',
        }))
        setSearch(prevMap => ({
            ...prevMap,
            administrative_area_level_1: '',
        }))
        setSearch(prevMap => ({
            ...prevMap,
            country: '',
        }))
        setSearch((prevMap) => ({
            ...prevMap,
            locality: '',
        }));
        setSearch((prevMap) => ({
            ...prevMap,
            postal_code: '',
        }));

        localStorage.setItem('searchType',JSON.stringify('skills'))
        localStorage.setItem('badgeId',JSON.stringify(null))
        localStorage.setItem('skillIds',JSON.stringify(null))
        localStorage.setItem('rating',JSON.stringify(null))
    }

    // console.log('Selected Rating : ', search);
    return (
        <>
            <Button type="button" className="btn btn-search" onClick={handleShowModal} data-bs-target="#exampleModal">
                <span>Tryck här för att söka</span>
                <img src={getPublicUrl + '/images/filter-icon.png'} alt="filter-icon" />
            </Button>

            {/*Search Modal Start*/}
            <Modal className="fillter-popup" size="md" aria-labelledby="contained-modal-title-vcenter" centered show={showModal} onHide={handleCloseModal}>
                <Modal.Body>
                    <Modal.Header closeButton>
                        <h2>Filter</h2>
                    </Modal.Header>
                    <div className="close-wrapper">
                        <div>
                            <button className="resetForm" onClick={(e) => handleCancelSearchForm(e)}>Avbryt</button>
                        </div>
                        <div>
                            <button className="resetForm" onClick={(e) => handleResetSearchForm(e)}>Återställ</button>
                        </div>
                    </div>
                    <h4>Söktyp</h4>
                    <Form className={`helpii-search-form`}>
                        <Form>
                        <Form.Group as={Row} className="mb-3 align-items-center">
                            <Col sm={12}>
                                <Form.Check className="d-inline-flex me-4 ps-0" type="radio" label="Skills" name="search_for" id="search_profiles" checked={searchTypeRadio === 'skills'} onChange={() => handleChangeSearchType('skills')} value="skills"/>
                                <Form.Check className="d-inline-block" type="radio" label="Annonser" name="search_for" id="search_annonser" checked={searchTypeRadio === 'annonser'} onChange={() => handleChangeSearchType('annonser')} value="annoser"/>
                            </Col>
                        </Form.Group>
                        <Form.Group as={Row} className="mb-3 align-items-center">
                            <Col sm={12}>
                                <Autocomplete placeholder='Search city' className={`location form-control`}
                                    apiKey='AIzaSyANGQiDmKPOHX5H5fUJQiuVsjhsL1Q3MtU'
                                    onPlaceSelected={(place) => {
                                        addressCallBackHandler(place);
                                    }}
                                    value={search.locality} onChange={handleCityChange}
                                />
                            </Col>
                        </Form.Group>
                    </Form>
                        <h4>Brickor</h4>
                        <ul className="badges-list mb-2">
                            {badgeOptions && badgeOptions.length > 0 ? (
                                badgeOptions.map((badge, index) => {
                                    const icon = badge.img != null ? `storage/badges/${badge.img}` : 'storage/badges/no_img.jpeg'
                                    const isChecked = badge.id === selectedBadges;
                                    return (
                                        <li key={index}>
                                            <input type="checkbox" id={`badge-${badge.id}`} onChange={() =>
                                                handleBadgeClick(badge.id)} checked={isChecked} style={{ display: 'none' }} />
                                            <label htmlFor={`badge-${badge.id}`}>
                                                <img src={`${getPublicUrl}/${icon}`} className="badge-image mb-2" id={`badge-${badge.id}`} alt={badge.badge_name}/>
                                                <p className={`helpii-bagde ${isChecked ? 'active' : ''}`}>{badge.badge_name}</p>
                                            </label>
                                        </li>
                                    )
                                })
                            ) : (
                                <li>
                                    <p>No badges found</p>
                                </li>
                            )}
                        </ul>
                        <h4>Betyg</h4>
                        <ul className="rating-list mb-4">
                            { ratings.map((rating, rindex) => {
                                const isChecked = rating === selectedRatings ? 'active' : '';
                                const image = rating === selectedRatings ? 'images/rating-star-filled.png' : 'images/star-unselect.png';
                                return(
                                   <>
                                        <li key={rindex} className={`${isChecked}`} onChange={() => handleRatingClick(rating)}>
                                            <input type="checkbox" id={`rating-${rating}`} style={{ display: 'none' }} className="rating-checkbox"/>
                                            <label className={`d-flex align-items-center`} htmlFor={`rating-${rating}`}  style={{ display: 'none' }}>
                                                <img src={`${getPublicUrl}/${image}`}  alt="star-icon" className={`me-1`}/>{rating}
                                            </label>
                                        </li>
                                   </>
                                );
                            }) }
                        </ul>
                        <ul className="skill-list mb-2">
                            {skillOptions && skillOptions.length > 0 ? (
                                skillOptions.map((skill, index) => {
                                    const icon = skill.img != null ? getPublicUrl + `/storage/skills/${skill.img}` : getPublicUrl + '/storage/skills/no_img.jpeg'
                                    const isChecked = selectedSkills === skill.id ? 'active' : '';
                                    const isActive = skill.id === selectedSkills;

                                    return (
                                        <li key={skill.id} className={`${isChecked}`}>
                                            <input type="checkbox" id={`skill-${skill.id}`} onChange={() => handleSkillCheckboxChange(skill.id)} style={{ display: 'none' }} checked={isActive} className="skill-radio"/>
                                            <div className="skill-inner">
                                                <label htmlFor={`skill-${skill.id}`}>
                                                    <img src={icon} className="skill-img" id={`skill-${skill.id}`} alt={skill.name} />
                                                    <span>{skill.name}</span>
                                                </label>
                                            </div>
                                            {isActive && (
                                                <div className="skill-active-icon">
                                                    <FaCheck />
                                                </div>
                                            )}
                                        </li>
                                    )
                                })
                            ) : (
                                <li>
                                    <span>No skills found</span>
                                </li>
                            )}
                        </ul>
                    </Form>
                </Modal.Body>
                <Modal.Footer className="justify-content-center">
                    {errors?.skillid && <p className="error-message">{errors.skillid[0]}</p>}
                    <button className="filtet-button outline-primary" onClick={handleSearch}>Filter</button>
                </Modal.Footer>
            </Modal>
            {/*Search Modal End*/}
        </>
    );
}