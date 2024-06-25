"use client"
import React, { useState, useEffect } from 'react';
import axios from 'axios'
import { Link } from 'react-router-dom';
import { Container, Row, Col, Modal, Form, Button, Card } from 'react-bootstrap';
import SearchModal from '@/components/Modals/SearchModal';
import { useRouter, useParams, useSearchParams } from 'next/navigation'
import {useAppContext} from '@/context'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL'];

// const FilterUsers = () => {
export default function UserDetails(props) {
    const router = useRouter();
    const searchParams = useSearchParams()
    const {appResults, setAppResults, userDetails, setUserDetails} = useAppContext()



    return (
        <>
            <div className="badges-section">
                <Container>
                  <Row>
                    <Col md={12}>
                      <ul className="profile-badges-list">
                        <li>
                          <img
                            src={getPublicUrl+"/images/profile-badge-01.png"}
                            alt="profile-badge-01"
                          />
                          <p>Ung företag</p>
                        </li>
                        <li>
                          <img
                            src={getPublicUrl+"/images/profile-badge-02.png"}
                            alt="profile-badge-02"
                          />
                          <p>Student</p>
                        </li>
                        <li>
                          <img
                            src={getPublicUrl+"/images/profile-badge-03.png"}
                            alt="profile-badge-03"
                          />
                          <p>Har swish</p>
                        </li>
                      </ul>
                    </Col>
                  </Row>
                </Container>
            </div>
            <div className="profile-section">
                <Container>
                    <Row>
                        <Col md={12}>
                            <div className="profile-wrapper d-flex align-items-center">
                                <div className="flex-shrink-0">
                                    <img className="profileimage" src={getPublicUrl+"/images/profile-image.png"}  alt="profile-image"/>

                                    <ul className="profile-rating">
                                        <li>
                                          <img src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                        </li>
                                        <li>
                                          <img src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                        </li>
                                        <li>
                                          <img src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                        </li>
                                        <li>
                                          <img src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                        </li>
                                        <li>
                                          <img src={getPublicUrl+"/images/star-icon.png"} alt="star-icon" />
                                        </li>
                                    </ul>
                                    {/*<Link to="/Reviews">*/}
                                        <small className="feedback-text">Läs feedback</small>
                                    {/*</Link>*/}
                                </div>
                                <div className="flex-grow-1 profile-content">
                                    <div className="profile-header">
                                        <h4>Jane Doe</h4>
                                        <ul className="profile-icons">
                                            <li>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="45" height="40" viewBox="0 0 45 40" fill="none">
                                                    <path d="M22.5 39.5L41.599 20.3617C43.792 18.1642 45 15.2422 45 12.1349C45 9.02699 43.792 6.1055 41.599 3.90799C39.406 1.71048 36.4905 0.5 33.389 0.5C30.2875 0.5 27.372 1.71048 25.179 3.90799L22.5 6.5925L19.821 3.90799C17.628 1.71048 14.712 0.5 11.611 0.5C8.50949 0.5 5.594 1.71048 3.401 3.90799C1.2075 6.1055 0 9.02699 0 12.1349C0 15.2427 1.2075 18.1642 3.401 20.3617L22.5 39.5ZM33.389 2.50411C35.956 2.50411 38.3695 3.50617 40.185 5.3249C42.0005 7.14363 43 9.56259 43 12.1349C43 14.7071 42 17.1256 40.185 18.9448L22.5 36.6662L4.815 18.9448C2.9995 17.1256 2 14.7071 2 12.1349C2 9.56259 2.9995 7.14413 4.815 5.3249C6.6305 3.50617 9.0435 2.50411 11.611 2.50411C14.178 2.50411 16.5915 3.50617 18.407 5.3249L22.5 9.42681L26.5935 5.3249C28.4085 3.50617 30.822 2.50411 33.389 2.50411Z" fill="#873D8F"/>
                                                </svg>
                                            </li>
                                            <li>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                                                  <path
                                                    d="M38.071 30.8344C35.6728 30.8344 33.5314 31.9628 32.1446 33.7124L20.846 27.4012C21.1005 26.6433 21.2396 25.8293 21.2396 24.9907C21.2396 24.1435 21.1004 23.3369 20.8378 22.5713L32.1269 16.2692C33.5057 18.0275 35.6557 19.1643 38.0624 19.1643C42.2184 19.1643 45.6115 15.78 45.6115 11.6151C45.6115 7.45035 42.2272 4.06602 38.0624 4.06602C33.8976 4.06602 30.5132 7.45035 30.5132 11.6151C30.5132 12.4618 30.6523 13.2763 30.9148 14.0347L19.6348 20.3365C18.2558 18.5698 16.1056 17.4416 13.6991 17.4416C9.54306 17.4416 6.15 20.8258 6.15 24.9907C6.15 29.1556 9.54314 32.5398 13.7078 32.5398C16.1147 32.5398 18.2648 31.4028 19.6518 29.6361L30.941 35.9468C30.678 36.7131 30.5305 37.5359 30.5305 38.3835C30.5305 42.5396 33.9148 45.9327 38.0797 45.9327C42.2445 45.9327 45.6288 42.5483 45.6288 38.3835C45.6288 34.2186 42.2357 30.8344 38.071 30.8344ZM38.071 6.61063C40.8363 6.61063 43.0842 8.85856 43.0842 11.6238C43.0842 14.389 40.8363 16.637 38.071 16.637C35.3058 16.637 33.0579 14.389 33.0579 11.6238C33.0579 8.85867 35.3143 6.61063 38.071 6.61063ZM13.7078 30.0039C10.9425 30.0039 8.69462 27.7559 8.69462 24.9907C8.69462 22.2255 10.9425 19.9775 13.7078 19.9775C16.473 19.9775 18.7209 22.2255 18.7209 24.9907C18.7209 27.7558 16.4645 30.0039 13.7078 30.0039ZM38.071 43.3881C35.3058 43.3881 33.0579 41.1401 33.0579 38.3749C33.0579 35.6097 35.3058 33.3617 38.071 33.3617C40.8363 33.3617 43.0842 35.6097 43.0842 38.3749C43.0842 41.1401 40.8363 43.3881 38.071 43.3881Z" fill="#873D8F" stroke="#873D8F" strokeWidth="0.2"/>
                                                </svg>
                                            </li>
                                        </ul>
                                    </div>
                                    <small>Om mig</small>
                                    <p>
                                        I am Jane Doe and I am a really good person with people
                                        skills. My ordinery job is Senior developer at high end firm
                                        but my passion is to help others. So that is why I am here
                                        on helpii.{" "}
                                    </p>
                                    <div className="profile-button-wrapper">
                                        {/*<Link className="link1">*/}
                                            <img className="mb-1" src={getPublicUrl+"/images/profile-button.png"} alt="profile-button"/>
                                              Fakturera utan företag
                                        {/*</Link>*/}
                                    </div>
                                </div>
                            </div>
                        </Col>
                    </Row>
                </Container>
            </div>
        </>
    );
};