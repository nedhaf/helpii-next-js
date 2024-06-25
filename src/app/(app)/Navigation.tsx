"use client"

import LoginLinks from '@/app/LoginLinks'
import ApplicationLogo from '@/components/ApplicationLogo'
import Dropdown from '@/components/Dropdown'
import Link from 'next/link'
import NavLink from '@/components/NavLink'
import ResponsiveNavLink, {
    ResponsiveNavButton,
} from '@/components/ResponsiveNavLink'
import { DropdownButton } from '@/components/DropdownLink'
import { useAuth } from '@/hooks/auth'
import { useRouter } from 'next/navigation'
import { useState } from 'react'
import { Container, Nav, Navbar, Offcanvas, Form } from 'react-bootstrap'
import {useAppContext} from '@/context'

const Navigation = ({ user, publicurl }) => {
    const {Authuser,ChatifyCounter} = useAppContext()
    const router = useRouter()

    const { logout } = useAuth()

    const [open, setOpen] = useState(false)

    const [show, setShow] = useState(false);
    const handleClose = () => setShow(false);
    const handleShow = () => setShow(true);


    const [shownotification, setShowNotification] = useState(false);
    const handleCloseNotification = () => setShowNotification(false);
    const handleShowNotification = () => setShowNotification(true);

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
    console.log('From (app) layout Nav chatifyCounter : ', chatifyCounter);
    // console.log('From (app) layout Nav : ', authUser);
    return (
        <>
            <Navbar collapseOnSelect expand="lg" className="custom-navbar">
                <Container fluid className="p-0">

                    <Navbar.Brand href="/"><img src={publicurl+"/images/helpii-header-logo.png"} alt="helpii-header-logo" /></Navbar.Brand>
                    <Navbar.Toggle aria-controls="responsive-navbar-nav" />
                    <Navbar.Collapse id="responsive-navbar-nav">
                    <Nav className="mx-auto">
                        <Nav.Link onClick={(e) => handleClick(e, `/filter-users`, null)}><img src={publicurl+"/images/header-icon-01.png"} alt="header-icon-01" /></Nav.Link>
                        { authUser ? (<><Nav.Link onClick={(e) => handleClick(e, `/filter-users`, null)}><img src={publicurl+"/images/header-icon-02.png"} alt="header-icon-02" /></Nav.Link>
                        <Nav.Link href="/chatify"><span className="chatify-total-unread">{chatifyCounter}</span><img src={publicurl+"/images/header-icon-03.png"} alt="header-icon-03" />
                        </Nav.Link></>) : null}

                        { !authUser ? <Nav.Link href="/login"><img src={publicurl+"/images/profile-icon-white.png"} alt="header-profile" /></Nav.Link> : <Nav.Link onClick={(e) => handleClick(e, `/user-profile`, authUser.slug)}>
                            { authUser.avatar_location ? (<>
                                <img src={`${publicurl}/storage/${authUser.avatar_location}`} alt={authUser.full_name} className={`nav-user-img-2`}/>
                            </>) :
                            (<>
                                <img src={publicurl+"/storage/dummy.png"} className={`nav-user-img-2`} alt={authUser.full_name}/>
                            </>) }
                        </Nav.Link> }

                    </Nav>
                    <Nav>
                        { authUser ? <Nav.Link onClick={handleShowNotification}><img src={publicurl+"/images/notification-icon.png"} alt="notification-icon" /></Nav.Link> : ''}
                    </Nav>
                    {/* Authentication */}
                    <Nav>
                        <LoginLinks isHomepage={false}/>
                    </Nav>
                    </Navbar.Collapse>
                </Container>
            </Navbar>
        </>
    );
}

export default Navigation
