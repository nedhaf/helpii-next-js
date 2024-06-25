"use client"
import useSWR from 'swr'
import axios from '@/lib/axios'
import { useEffect } from 'react'
import { useParams, useRouter } from 'next/navigation'
import {useAppContext} from '@/context'

export const useAuth = ({ middleware, redirectIfAuthenticated } = {}) => {
    const router = useRouter()
    const params = useParams()
    const {Authuser, setAuthuser, AuthToken, setAuthToken, ChatifyCounter, setChatifyCounter} = useAppContext()

    const { data: user, error, mutate } = useSWR('/api/user', () =>
        axios
            .get('/api/user')
            // .then(res => res.data)
            .then((res) => {
                // console.log("chatify=",res.data)
                setAuthToken(res.data.token);
                setChatifyCounter(res.data.chatify_counter);
                // setAuthuser({ 'user': res.data.user ? res.data.user : null });
                return res.data.user;

            })
            .catch(error => {
                if (error.response.status !== 409) throw error

                router.push('/verify-email')
            }),
    )

    const csrf = () => axios.get('/sanctum/csrf-cookie')

    const register = async ({ setErrors, ...props }) => {
        await csrf()

        setErrors([])

        axios
            .post('/register', props)
            .then(() => mutate())
            .catch(error => {
                if (error.response.status !== 422) throw error

                setErrors(error.response.data.errors)
            })
    }

    const login = async ({ setErrors, setStatus, ...props }) => {
        await csrf()

        setErrors([])
        setStatus(null)

        // .then(() => mutate())
        axios
            .post('api/login', props)
            .then((result) => {
                mutate()
                // setAuthuser({ 'user': result.data.user });
                if( result.data.status === 401 ) {
                    setErrors(result.data.message);
                }
                setAuthToken(result.data.token);
            })
            .catch(error => {
                if (error.response.status !== 422) throw error

                setErrors(error.response.data.errors)
            })
    }

    const forgotPassword = async ({ setErrors, setStatus, email }) => {
        await csrf()

        setErrors([])
        setStatus(null)

        axios
            .post('/forgot-password', { email })
            .then(response => setStatus(response.data.status))
            .catch(error => {
                if (error.response.status !== 422) throw error

                setErrors(error.response.data.errors)
            })
    }

    const resetPassword = async ({ setErrors, setStatus, ...props }) => {
        await csrf()

        setErrors([])
        setStatus(null)

        axios
            .post('/reset-password', { token: params.token, ...props })
            .then(response =>
                router.push('/login?reset=' + btoa(response.data.status)),
            )
            .catch(error => {
                if (error.response.status !== 422) throw error

                setErrors(error.response.data.errors)
            })
    }

    const resendEmailVerification = ({ setStatus }) => {
        axios
            .post('/email/verification-notification')
            .then(response => setStatus(response.data.status))
    }

    const logout = async () => {
        if (!error) {
            await axios.post('api/logout').then(() => mutate())
        }

        window.location.pathname = '/login'
    }

    useEffect(() => {
            // console.log('Loogedin users: ', user);
        if (middleware === 'guest' && redirectIfAuthenticated && user)
            // router.push('/user-profile/'+user.slug);
            router.push(redirectIfAuthenticated)
        if ( window.location.pathname === '/verify-email' && user?.email_verified_at )
            router.push(redirectIfAuthenticated)
        if (middleware === 'auth' && error) logout()

        if( user )
            setAuthuser({ 'user': user });
    }, [user, error])

    return {
        user,
        register,
        login,
        forgotPassword,
        resetPassword,
        resendEmailVerification,
        logout,
    }
}
