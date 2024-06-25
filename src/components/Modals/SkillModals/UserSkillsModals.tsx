'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
// import axios from 'axios'
import axios from '@/lib/axios'
import { Container, Row, Button, Col, Modal, Form, Alert, Card } from 'react-bootstrap'
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import Select from 'react-select';

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function UserSkillsModal({ activeSModal, onSModalClose, userId, userProfile, children }) {
    const [isUserSkillStep1Open, setUserSkillStep1IsOpen] = useState(activeSModal);
    const [isDescriptionModalOpen, setDescriptionModalOpen] = useState(false);
    const [skillOptions, setSkillOptions] = useState([])
    const [selectedSkill, setSelectedSkill] = useState(null);
    const [selectedOption, setSelectedOption] = useState(null);
    const [errors, setErrors] = useState(null)
    const [successMessage, setSuccessMessage] = useState(null)
    const [isLoading, setIsLoading] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);

    useEffect(() => {
        allSkills()
        setUserSkillStep1IsOpen(activeSModal);
    }, [activeSModal]);

    // Get all skills
    const allSkills = () => {
        const config = {
            headers: {
                'content-type': 'application/json',
                'x-inertia': 'true',
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

    const handleClose = () => {
        setUserSkillStep1IsOpen(false);
        onSModalClose && onSModalClose();
    };

    async function handleSkillChange(e) {
        const selectedValue = e.target.value;
        const selectedText = e.target.options[e.target.selectedIndex].textContent;
        localStorage.setItem('user_skill_id', selectedValue);
        localStorage.setItem('user_skill_name', selectedText);
        console.log('USer Skill :', e.target.value);
        console.log('USer Skill :', e.target.options[e.target.selectedIndex].textContent);
    }

    async function handleSubmit(e) {
        e.preventDefault();
        setIsLoading(true);
    }

    return (
        <>
            <Modal className={`user-edit-availability-modal user-edit-availability-modal-${userId}`} centered backdrop="static" size="md" show={isUserSkillStep1Open} onHide={handleClose}>
                <Modal.Header closeButton>
                </Modal.Header>
                <Modal.Body>
                    { successMessage && (<Alert variant="success" className="mt-3" dismissible>
                        <FaCheck className="me-2" /> {successMessage}
                    </Alert>) }
                    { errors && (<Alert variant="danger" className="mt-3" dismissible>
                        <FaCircleXmark className="me-2" /> {errors}
                    </Alert>) }
                    <Form onSubmit={handleSubmit}>
                        <select className="form-select user-skill" value={selectedSkill} onChange={(e) => handleSkillChange(e)}>
                            {skillOptions.map((skill) => (
                                <option key={skill.id || skill.value} value={skill.id || skill.value}>
                                    {skill.name || skill.label}
                                </option>
                            ))}
                        </select>
                        <div className={`user-availability-btns mt-3 d-flex justify-content-center`}>
                            {/*<button type="submit" className="save-button outline-primary" disabled={isLoading || !selectedSkill || !errors}>{isLoading ? 'Confirming...' : 'Next'}</button>*/}
                            <button type="button" className="save-button outline-primary">Next</button>
                        </div>
                    </Form>
                </Modal.Body>
            </Modal>
        </>
    );
}