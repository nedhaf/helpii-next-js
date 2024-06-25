'use client'

import React, { useState, useEffect, useRef } from 'react'
import { Link, useHistory } from 'react-router-dom'
import axios from 'axios'
import { Container, Row, Button, Col, Modal, Form } from 'react-bootstrap'
import { FaCheck } from 'react-icons/fa6'
import { useRouter } from 'next/navigation'
import UserLanguageModal from "@/components/Modals/UserSettingsModals/UserLanguageModal";

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

type Props = {
  show: boolean | undefined;
  handleClose?: () => void;
  handleShow?: () => void;
};

export default function UserProfileModal({ showModal, onClose, userId }) {
    const [isOpen, setIsOpen] = useState(showModal);
    const [showLanguageModal, setShowLanguageModal] = useState(false);

    useEffect(() => {
        setIsOpen(showModal);
    }, [showModal]);

    const handleClose = () => {
        setIsOpen(false);
        onClose && onClose();
    };

    return (
        <>
            <Modal className={`user-edit-profile-modal user-edit-profile-modal-${userId}`} centered size="lg" show={isOpen} onHide={handleClose}>
                <Modal.Header closeButton>
                    <h2>Mina Inställningar</h2>
                </Modal.Header>
                <Modal.Body>
                    {/* Add your modal content here  */}
                    <ul className="user-profile-settings-list mb-2">
                        <li className={`user-language-li`}>
                            <div className={`user-language`}>
                                <img src={`${getPublicUrl}/images/helpii-user-settings/user-language.svg`} alt="Language Setting" />
                                <span className={`ms-4`}>
                                    <img src={`${getPublicUrl}/images/helpii-user-settings/flags/bharat.svg`} width={`49px`} height={`35px`} alt="Bharat" />
                                    <span className={`ms-3`}>Hindi</span>
                                </span>
                            </div>
                            <div className={`edit-setting`} onClick={() => setShowLanguageModal(true)}>
                                <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                            </div>
                        </li>
                        <li className={`user-notification-li`}>
                            <div className={`user-notification`}>
                                <img src={`${getPublicUrl}/images/helpii-user-settings/user-notification.svg`} alt="Notification Setting" />
                                <span className={`ms-4`}>Testing</span>
                            </div>
                            <div className={`edit-setting`}>
                                <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                            </div>
                        </li>
                        <li className={`user-delete-profile-li`}>
                            <div className={`user-delete-profile`}>
                                <img src={`${getPublicUrl}/images/helpii-user-settings/delete-profile.png`} alt="Delete Profile" />
                                <span className={`ms-4`}>Testing</span>
                            </div>
                            <div className={`edit-setting`}>
                                <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                            </div>
                        </li>
                        <li className={`user-currency-li`}>
                            <div className={`user-currency`}>
                                <img src={`${getPublicUrl}/images/helpii-user-settings/user-currency.svg`} alt="Delete Profile" />
                                <span className={`ms-4`}>Testing</span>
                            </div>
                            <div className={`edit-setting`}>
                                <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                            </div>
                        </li>
                        <li className={`user-availability-li`}>
                            <div className={`user-availability`}>
                                <img src={`${getPublicUrl}/images/helpii-user-settings/user-availability.svg`} alt="Delete Profile" />
                                <span className={`ms-4`}>Testing</span>
                            </div>
                            <div className={`edit-setting`}>
                                <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                            </div>
                        </li>
                        <li className={`user-info-li`}>
                            <div className={`user-info`}>
                                <img src={`${getPublicUrl}/images/helpii-user-settings/user-info.svg`} alt="Delete Profile" />
                                <span className={`ms-4`}>Testing</span>
                            </div>
                            <div className={`edit-setting`}>
                                <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                            </div>
                        </li>
                        <li className={`user-badge-li`}>
                            <div className={`user-badge`}>
                                <img src={`${getPublicUrl}/images/helpii-user-settings/user-badge.svg`} alt="Delete Profile" />
                                <span className={`ms-4`}>Testing</span>
                            </div>
                            <div className={`edit-setting`}>
                                <img src={`${getPublicUrl}/images/left-caret.png`} alt="Left" />
                            </div>
                        </li>
                    </ul>
                    {showLanguageModal && <UserLanguageModal showLangSettingModal={showLanguageModal} onLangSettingClose={() => setShowLanguageModal(false)} userId={userId}/>}
                </Modal.Body>
                {/*<Modal.Footer>
                    <Button variant="secondary" onClick={handleClose}>
                        Close
                    </Button>
                    <Button variant="primary">Save Changes</Button>
                </Modal.Footer>*/}
            </Modal>
        </>
    );
}