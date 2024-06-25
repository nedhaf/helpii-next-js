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

    const [showModal, setShowModal] = useState(false)
    const [selectedRadio, setSelectedRadio] = useState('skills')
    const [skillOptions, setSkillOptions] = useState([])
    const [badgeOptions, setBadgeOptions] = useState([])
    const [selectedBadges, setSelectedBadges] = useState([])
    const [selectedSkills, setSelectedSkills] = useState([])
    const [selectedRatings, setSelectedRatings] = useState([])
    const [search, setSearch] = useState({
        skillid: '',
        profile_badge: '',
        where: '',
        oldwhere: '',
        street_number: '',
        route: '',
        locality: '', // city
        administrative_area_level_1: '', // state
        country: '',
        postal_code: '',
        fulladdress: '',
        lng: '',
        lat: '',
        search_for: 'skills',
    })
    const [errors, setErrors] = useState([])
    const [whatError, setWhatError] = useState('')
    const [placeError, setPlaceError] = useState('')
    const [noRecordError, setNoRecordError] = useState('')
    const [searchQuery, setSearchQuery] = useState('')
    const [searchBy, setSearchBy] = useState('')
    const [address, setAddress] = useState('')
    const [showSuggestions, setShowSuggestions] = useState(false)
    // const [searchResults, setSearchResults] = useState(null);
    // const libraries = libraries;
    const searchBox = useRef(null)

    const ratings = [1, 2, 3, 4, 5]

    useEffect(() => {
        console.log('Seacrh for : ', search.search_for)
        allSkills()
        allBadges()

        // Retrieve stored data from localStorage
        const storedBadges = JSON.parse(localStorage.getItem('badgeIds'))
        if (storedBadges && Array.isArray(storedBadges)) {
            setSelectedBadges(storedBadges)
        }
        const storedSkills = JSON.parse(localStorage.getItem('skillIds'))
        if (storedSkills && Array.isArray(storedSkills)) {
            setSelectedSkills(storedSkills)
        }
    }, [])

    // Search type
    // localStorage.setItem('search_for', search.search_for);
    const handleRadioChange = value => {
        const _search = search
        _search['search_for'] = value
        setSelectedRadio(value)
        localStorage.search_for = value
        setSearch(prevSearch => ({
            ...prevSearch,
            search_for: value,
        }))
    }

    // Get all skills
    const allSkills = () => {
        const config = {
            headers: {
                'content-type': 'application/json',

            },
        }

        axios
            .get(getPublicUrl + '/getSkills', config)
            .then(response => {
                setSkillOptions(response.data.results)
            })
            .catch(e => {
                setErrors([...errors, e])
            })
    }

    // Get all badges
    const allBadges = () => {
        const config = {
            headers: {
                'content-type': 'application/json',
            },
        }

        axios
            .get(getPublicUrl + '/getBadges', config)
            .then(response => {
                setBadgeOptions(response.data.results)
            })
            .catch(e => {
                setErrors([...errors, e])
            })
    }

    // Badge checkbox handle
    const handleBadgeCheckboxChange = badgeId => {
        // Code for radio
        setSelectedBadges(prevSelectedBadges => {
            const isSelected = prevSelectedBadges.includes(badgeId)

            if (isSelected) {
                // If the badge is already selected, remove it from the list
                return []
            } else {
                // If the badge is not selected, add it to the list
                return [badgeId]
            }
        })

        setSearch(prevSearch => ({
            ...prevSearch,
            profile_badge: badgeId,
        }))
    }
    // Skills checkbox handle
    const handleSkillCheckboxChange = skillId => {
        setSelectedSkills(prevSelectedSkillId => {
            const isSelected = prevSelectedSkillId.includes(skillId)

            if (isSelected) {
                return []
            } else {
                return [skillId]
            }
        })

        setSearch(prevSearch => ({
            ...prevSearch,
            skillid: skillId,
        }))
        // Code for checkbox
    }

    const handleShowModal = () => {
        setShowModal(true)
    }

    const handleCloseModal = () => {
        setShowModal(false)
    }

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

    const handleSearch = e => {
        e.preventDefault();
        axios.post(getPublicUrl+'/api/search-users', search).then(response => {
            const searchResults = response
            if( response.data.status == 200 ) {
                // if( response.data.errors ) {
                //     setErrors(response.data.errors)
                // } else {
                    setErrors(null)
                    setAppResults({ results: searchResults });
                    setShowModal(false)
                // }
            } else {
                if( response.data.errors || response.data.errors != null ) {
                    setErrors(response.data.errors)
                }
                // console.log('Search res Else : ', response);
            }
            router.push('/filter-users');
        })
    }
    return (
        <>
            <Button
                type="button"
                className="btn btn-search"
                onClick={handleShowModal}
                data-bs-target="#exampleModal">
                <span>Tryck här för att söka</span>
                <img
                    src={getPublicUrl + '/images/filter-icon.png'}
                    alt="filter-icon"
                />
            </Button>
            <Modal
                className="fillter-popup"
                size="md"
                aria-labelledby="contained-modal-title-vcenter"
                centered
                show={showModal}
                onHide={handleCloseModal}>
                <Modal.Body>
                    <Modal.Header closeButton>
                        <h2>Filter</h2>
                    </Modal.Header>
                    <div className="close-wrapper">
                        <div>
                            <a>Avbryt</a>
                        </div>
                        <div>
                            <a className="resetForm">Återställ</a>
                        </div>
                    </div>
                    <h4>Söktyp</h4>
                    <Form>
                        <Form.Group
                            as={Row}
                            className="mb-3 align-items-center">
                            <Col sm={12}>
                                <Form.Check
                                    className="d-inline-flex me-4 ps-0"
                                    type="radio"
                                    label="Skills"
                                    name="search_for"
                                    id="search_profiles"
                                    checked={selectedRadio === 'skills'}
                                    onChange={() => handleRadioChange('skills')}
                                    value="skills"
                                />
                                <Form.Check
                                    className="d-inline-block"
                                    type="radio"
                                    label="Annonser"
                                    name="search_for"
                                    id="search_annonser"
                                    checked={selectedRadio === 'annonser'}
                                    onChange={() =>
                                        handleRadioChange('annonser')
                                    }
                                    value="annoser"
                                />
                            </Col>
                        </Form.Group>
                        <Form.Group
                            as={Row}
                            className="mb-3 align-items-center">
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
                                const icon =
                                    badge.img != null
                                        ? `storage/badges/${badge.img}`
                                        : 'storage/badges/no_img.jpeg'
                                const isActive = selectedBadges.includes(
                                    badge.id,
                                )
                                localStorage.setItem(
                                    'badgeIds',
                                    JSON.stringify(selectedBadges),
                                )
                                return (
                                    <li key={badge.id}>
                                        <input
                                            type="radio"
                                            id={`badge-${badge.id}`}
                                            checked={selectedBadges.includes(
                                                badge.id,
                                            )}
                                            onChange={() =>
                                                handleBadgeCheckboxChange(
                                                    badge.id,
                                                )
                                            }
                                            style={{ display: 'none' }} // Hide the checkbox
                                        />
                                        <label htmlFor={`badge-${badge.id}`}>
                                            <img
                                                src={
                                                    getPublicUrl +
                                                    `/storage/badges/${
                                                        badge.img ||
                                                        'no_img.jpeg'
                                                    }`
                                                }
                                                className="badge-image mb-2"
                                                id={`badge-${badge.id}`}
                                                alt={badge.badge_name}
                                            />
                                            <p
                                                className={`${
                                                    isActive ? 'active' : ''
                                                }`}>
                                                {badge.badge_name}
                                            </p>
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
                        <li>
                            <input
                                type="radio"
                                id="rating-1"
                                style={{ display: 'none' }} // Hide the radio button
                                className="rating-radio"
                            />
                            <label htmlFor="rating-1">
                                <img
                                    src={
                                        getPublicUrl +
                                        '/images/star-unselect.png'
                                    }
                                    alt="star-icon"
                                />
                                1
                            </label>
                        </li>
                        <li>
                            <input
                                type="radio"
                                id="rating-2"
                                style={{ display: 'none' }} // Hide the radio button
                                className="rating-radio"
                            />
                            <label htmlFor="rating-2">
                                <img
                                    src={
                                        getPublicUrl +
                                        '/images/star-unselect.png'
                                    }
                                    alt="star-icon"
                                />
                                2
                            </label>
                        </li>
                        <li>
                            <input
                                type="radio"
                                id="rating-3"
                                style={{ display: 'none' }} // Hide the radio button
                                className="rating-radio"
                            />
                            <label htmlFor="rating-2">
                                <img
                                    src={
                                        getPublicUrl +
                                        '/images/star-unselect.png'
                                    }
                                    alt="star-icon"
                                />
                                3
                            </label>
                        </li>
                        <li>
                            <input
                                type="radio"
                                id="rating-4"
                                style={{ display: 'none' }} // Hide the radio button
                                className="rating-radio"
                            />
                            <label htmlFor="rating-2">
                                <img
                                    src={
                                        getPublicUrl +
                                        '/images/star-unselect.png'
                                    }
                                    alt="star-icon"
                                />
                                4
                            </label>
                        </li>
                        <li>
                            <input
                                type="radio"
                                id="rating-5"
                                style={{ display: 'none' }} // Hide the radio button
                                className="rating-radio"
                            />
                            <label htmlFor="rating-2">
                                <img
                                    src={
                                        getPublicUrl +
                                        '/images/star-unselect.png'
                                    }
                                    alt="star-icon"
                                />
                                5
                            </label>
                        </li>
                    </ul>
                    <ul className="skill-list mb-2">
                        {skillOptions && skillOptions.length > 0 ? (
                            skillOptions.map((skill, index) => {
                                const icon =
                                    skill.img != null
                                        ? getPublicUrl +
                                          `/storage/skills/${skill.img}`
                                        : getPublicUrl +
                                          '/storage/skills/no_img.jpeg'
                                const isActive = selectedSkills.includes(
                                    skill.id,
                                )
                                localStorage.setItem(
                                    'skillIds',
                                    JSON.stringify(selectedSkills),
                                )
                                return (
                                    <li
                                        key={skill.id}
                                        className={`${
                                            isActive ? 'active' : ''
                                        }`}>
                                        <input
                                            type="radio"
                                            id={`skill-${skill.id}`}
                                            checked={selectedSkills.includes(
                                                skill.id,
                                            )}
                                            onChange={() =>
                                                handleSkillCheckboxChange(
                                                    skill.id,
                                                )
                                            }
                                            style={{ display: 'none' }}
                                            className="skill-radio"
                                        />
                                        <div className="skill-inner">
                                            <label
                                                htmlFor={`skill-${skill.id}`}>
                                                <img
                                                    src={icon}
                                                    className="skill-img"
                                                    id={`skill-${skill.id}`}
                                                    alt={skill.name}
                                                />
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
                </Modal.Body>
                <Modal.Footer className="justify-content-center">
                    {errors?.skillid && <p className="error-message">{errors.skillid[0]}</p>}
                    <button className="filtet-button outline-primary" onClick={handleSearch}>Filter</button>
                    {/*<Button
                        className="filtet-button"
                        variant="outline-primary"
                        onClick={handleSearch}>
                        Filter
                    </Button>*/}
                </Modal.Footer>
            </Modal>
        </>
    )
}