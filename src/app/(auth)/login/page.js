'use client'
// import "../../globals.css";
// import Button from '@/components/Button'
import Input from '@/components/Input'
import InputError from '@/components/InputError'
// import Label from '@/components/Label'
import Link from 'next/link'
import { useAuth } from '@/hooks/auth'
import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import AuthSessionStatus from '@/app/(auth)/AuthSessionStatus'
import { Container, Row, Col, Button, Card,Alert } from "react-bootstrap";
import Form from 'react-bootstrap/Form';
import styles from '@/app/style/style.module.css';
import { FaCheck, FaCircleXmark } from 'react-icons/fa6'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL'];

const Login = () => {
    const router = useRouter()

    const { login } = useAuth({
        middleware: 'guest',
        redirectIfAuthenticated: '/',
    })

    const [email, setEmail] = useState('')
    const [password, setPassword] = useState('')
    const [shouldRemember, setShouldRemember] = useState(false)
    const [errors, setErrors] = useState([])
    const [status, setStatus] = useState(null)

    useEffect(() => {
        if (router.reset?.length > 0 && errors.length === 0) {
            setStatus(atob(router.reset))
        } else {
            setStatus(null)
        }
    })

    const submitForm = async event => {
        event.preventDefault()

        login({
            email,
            password,
            remember: shouldRemember,
            setErrors,
            setStatus,
        })
    }
    const BgImg = `url(${getPublicUrl}/images/login-bg.png)`;
    console.log('Login Error : ', errors);
    return (
        <>
            <AuthSessionStatus className="mb-4" status={status} />
            <div className={`login-background-image-wrapper`} style={{backgroundImage: `${BgImg}`}}>
                <Container>
                    <Row className={`justify-content-end`}>
                        <Col md={6}>
                            <div className={`login-wrapper`}>
                                <h2 align="center">Welcome</h2>
                                <p align="center" className="mb-5">Choose how you want to Sign in!</p>
                                <Row>
                                    <Col md={12}>
                                        {errors != '' && errors && (
                                            <Alert variant="danger" className="" dismissible>
                                                <FaCircleXmark className="me-2" /> {errors}
                                            </Alert>
                                        )}
                                        <form onSubmit={submitForm}>
                                            <Form.Group className="mb-3" controlId="formGroupEmail">
                                                <div className="input-icon">
                                                    <img className="icon" src={`${getPublicUrl}/images/email.svg`} />
                                                </div>
                                                {/*<Form.Control className="custom-control" size="lg" type="email" placeholder="Enter your email" />*/}
                                                <Input id="email" type="email" value={email} size="lg" className="custom-control form-control form-control-lg" onChange={event => setEmail(event.target.value)} placeholder="Enter your email" required autoFocus/>
                                                <InputError messages={errors.email} className="mt-2" />
                                            </Form.Group>
                                            <Form.Group className="mb-3" controlId="formGroupPassword">
                                                <div className="input-icon">
                                                    <img className="icon" src={`${getPublicUrl}/images/lock.svg`} />
                                                </div>
                                                {/*<Form.Control className="custom-control " size="lg" type="password" placeholder="Password" />*/}
                                                <Input id="password" type="password" value={password} className="custom-control form-control form-control-lg" onChange={event => setPassword(event.target.value)} required autoComplete="current-password" placeholder="Password" />
                                                <div className="input-icon">
                                                    <img className="right-icon" src={`${getPublicUrl}/images/eye-closed.svg`} />
                                                </div>
                                                <InputError messages={errors.password} className="mt-2" />
                                            </Form.Group>

                                            <div className="links-wrapper">
                                                <Form.Group id="formGridCheckbox">
                                                    <Form.Check type="checkbox" label="Remember Me" onChange={event =>
                                                        setShouldRemember(event.target.checked)
                                                    }/>
                                                </Form.Group>
                                                <a className="forgot-link" href="#">Forgot Password ?</a>
                                            </div>

                                            <div className="buttons-wrapper">
                                                <Button className="login-button" type="submit">
                                                    Sign In
                                                </Button>
                                                <div id="seperator"><span id="divider"><span className="divider left"></span>OR<span className="divider right"></span></span>
                                                </div>
                                                <div className="login-button mb-3">

                                                    <Button className="socialmedia-btn mb-3"><img src={`${getPublicUrl}/images/google-icon.svg`} className="me-3" />Continue with Google</Button>
                                                    <Button className="socialmedia-btn"><img src={`${getPublicUrl}/images/facebook-icon.svg`} className="me-3" />Continue with Facebook</Button>
                                                </div>
                                                <p className="text-center">Don’t have an account? <a className="link">Sign Up</a></p>
                                                <img className="brand-logo" src={`${getPublicUrl}/images/helpii-login-logo.png`} />
                                            </div>
                                        </form>
                                    </Col>
                                </Row>
                            </div>
                        </Col>
                    </Row>
                </Container>
            </div>
        </>
    )
}

export default Login
