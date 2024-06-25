"use client"
import { createContext, useContext, useState } from 'react';
import { useAuth } from '@/hooks/auth'

// Create the context
const UserContext = createContext<any>(undefined);
const AuthContext = createContext<any>(undefined);

// Create a context provider
export function AppWrapper({ children } : {
    children: React.ReactNode;
}) {
    const [appResults, setAppResults] = useState()
    const [userDetails, setUserDetails] = useState()
    const [Authuser, setAuthuser] = useState(null)
    const [AuthToken, setAuthToken] = useState(null)
    const [ChatifyCounter, setChatifyCounter] = useState(0)

    return (
        <UserContext.Provider value={{ appResults, setAppResults, userDetails, setUserDetails, Authuser, setAuthuser, AuthToken, setAuthToken, ChatifyCounter, setChatifyCounter}}>
            {children}
        </UserContext.Provider>
    )
}

export function useAppContext(){
    return useContext(UserContext);
}
