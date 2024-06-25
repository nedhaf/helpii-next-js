"use client"
import LoginLinks from '@/app/LoginLinks'
import { loadEnvConfig } from '@next/env'
import { Container, Row, Button, Col, Modal, Form, Nav, Navbar, Offcanvas, NavDropdown } from 'react-bootstrap'
import '@/app/style/style.css'
import '@/app/style/bootstrap.css'
// import 'bootstrap/dist/css/bootstrap.css';

import SearchModal from '@/components/Modals/SearchModal'
import {useAppContext} from '@/context'
import Navigation from '@/app/Navigation'
import { useAuth } from '@/hooks/auth'
import { useRouter } from 'next/navigation'
import { useState } from 'react'
import Dropdown from '@/components/Dropdown'
import Link from 'next/link'
import NavLink from '@/components/NavLink'
import ResponsiveNavLink, {
    ResponsiveNavButton,
} from '@/components/ResponsiveNavLink'
import { DropdownButton } from '@/components/DropdownLink'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

export default function Home() {
    const {Authuser,ChatifyCounter} = useAppContext()
    const router = useRouter()
    const { user } = useAuth()
    const { logout } = useAuth()

    const [open, setOpen] = useState(false)

    const [show, setShow] = useState(false);
    const handleClose = () => setShow(false);
    const handleShow = () => setShow(true);


    const [shownotification, setShowNotification] = useState(false);
    const handleCloseNotification = () => setShowNotification(false);
    const handleShowNotification = () => setShowNotification(true);

    // console.log('Users : ', user);

    const handleClick = (e, path, para) => {
        if (path === "/user-profile") {
            router.push('/user-profile/'+para);
        }

        if (path === "/filter-users") {
            router.push('/filter-users');
        }
    };

    const authUser = Authuser ? Authuser.user : null;
    const chatifyCounter = ChatifyCounter ? ChatifyCounter : 0;
    // console.log('Auth User : ', authUser);
  return (
        <>
            <div>
                <Container>
                    <Row>
                        {/*<Navigation user={user} publicurl={getPublicUrl} />*/}

                        <Navbar collapseOnSelect expand="lg" className="">
                            <Container fluid className="p-0">
                                <Navbar.Brand href="/"><img src={getPublicUrl+'/storage/site-settings/site_logo_1706010599.png'} alt="helpii-header-logo" /></Navbar.Brand>
                                <Navbar.Toggle aria-controls="responsive-navbar-nav" />
                                <Navbar.Collapse id="responsive-navbar-nav">
                                <Nav className="mx-auto">
                                    <Nav.Link onClick={(e) => handleClick(e, `/filter-users`, null)} className="me-3"><img src={getPublicUrl+"/images/usersall.png"} alt="header-icon-01" /></Nav.Link>
                                    { authUser ? (<><Nav.Link onClick={(e) => handleClick(e, `/filter-users`, null)} className="me-3"><img src={getPublicUrl+"/images/fav-con.png"} alt="header-icon-02" /></Nav.Link>
                                    <Nav.Link  href="/chatify" className="me-3"><span className="chatify-total-unread">{chatifyCounter}</span><img className={`navigation-user-profile`} src={getPublicUrl+"/images/chat-round.png"} alt="header-icon-03" />
                                    </Nav.Link></>) : null}
                                    { !authUser ? <Nav.Link href="/login" className="me-3"><img src={getPublicUrl+"/images/interface-user-circle.png"} alt="header-profile" /></Nav.Link> : <Nav.Link onClick={(e) => handleClick(e, `/user-profile`, authUser.slug)} className="me-3 home-profile-img-a">
                                        { authUser.avatar_location ? (<>
                                            <img src={`${getPublicUrl}/storage/${authUser.avatar_location}`} alt={authUser.full_name} className={`nav-user-img`}/>
                                        </>) :
                                        (<>
                                            <img src={getPublicUrl+"/storage/dummy.png"} className={`nav-user-img`} alt={authUser.full_name}/>
                                        </>) }

                                    </Nav.Link> }

                                </Nav>
                                {/* Authentication */}
                                <Nav>
                                    <LoginLinks isHomepage={true}/>
                                </Nav>
                                </Navbar.Collapse>
                            </Container>
                        </Navbar>
                    </Row>
                </Container>
            </div>
            <section className="banner-section">
                <Container>

                    <Row>
                        <Col md={6}>
                            <div className="banner-leftside">
                                <h2>Vi skapade appen</h2>
                                <h4>Sök profiler</h4>
                                <SearchModal/>
                                <div className="button-wrapper">
                                    <h2>
                                        ladda ner <span>helpii</span> idag.
                                    </h2>
                                    <img
                                        className="appstore-image me-4"
                                        src={
                                            getPublicUrl +
                                            '/images/app-store-button.png'
                                        }
                                        alt="app-store-button"
                                    />
                                    <img
                                        className="playstore-image"
                                        src={
                                            getPublicUrl +
                                            '/images/google-play-button.png'
                                        }
                                        alt="google-play-button"
                                    />
                                </div>
                            </div>
                            <img
                                className="big-arrow"
                                src={getPublicUrl + '/images/big-arrow.png'}
                                alt="big-arrow"
                            />
                        </Col>
                        <Col md={6}>
                            <img
                                className="banner-image"
                                src={getPublicUrl + '/images/mobile-banner.png'}
                                alt="helpii-logo"
                            />
                        </Col>
                    </Row>
                </Container>
            </section>
        </>
    );
}
