'use client'

import Link from 'next/link'
import { useAuth } from '@/hooks/auth'

// const LoginLinks = (isHomepage) => {
export default function LoginLinks({isHomepage}) {
    const { user } = useAuth({ middleware: 'guest' })
    const { logout } = useAuth()

    const divMainClass = '';
    console.log('Login links : ', isHomepage);

    if( isHomepage ) {
        return (
            <div className={`hidden fixed top-0 right-0 px-6 py-4 sm:block helpii-custom-auth-header`}>
                {user ? (
                    <button
                        onClick={logout}
                        className="ml-4 text-sm text-gray-700 underline">
                        Logout
                    </button>
                ) : (
                    <>
                        <Link
                            href="/login"
                            className="text-sm text-gray-700 underline me-3">
                            Login
                        </Link>

                        <Link
                            href="/register"
                            className="ml-4 text-sm text-gray-700 underline">
                            Register
                        </Link>
                    </>
                )}
            </div>
        )
    } else {
        return (
            <div className={`hidden fixed top-0 right-0 px-6 py-4 sm:block helpii-custom-auth-in-page-header`}>
                {user ? (
                    <button
                        onClick={logout}
                        className="ml-4 text-sm text-gray-700 underline">
                        Logout
                    </button>
                ) : (
                    <>
                        <Link
                            href="/login"
                            className="text-sm text-gray-700 underline me-3">
                            Login
                        </Link>

                        <Link
                            href="/register"
                            className="ml-4 text-sm text-gray-700 underline">
                            Register
                        </Link>
                    </>
                )}
            </div>
        )
    }
};

// export default LoginLinks
