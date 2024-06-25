'use client'

import { useAuth } from '@/hooks/auth'
import Navigation from '@/app/(app)/Navigation'
// import Loading from '@/app/(app)/Loading'

const AppLayout = ({ children, header, ...props }) => {
    // console.log('App layout props : ', {...props});
    const { user } = useAuth()
    const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL'];
    // if (!user) {
    //     return <Loading />
    // }

    return (
        <>
            <div className="min-h-screen bg-gray-100">
                <header className="bg-white shadow">
                    <Navigation user={user} publicurl={getPublicUrl} />
                </header>
                <div className="main">
                    {children}
                </div>
            </div>
        </>
    )
}

export default AppLayout
