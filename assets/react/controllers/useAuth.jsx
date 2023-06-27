import axios from "axios";

import React, {createContext, useContext, useEffect} from "react";
import { useState } from "react";

const AuthContext = createContext({
    auth: null,
    setAuth: () => {},
    user: null,
});

export default function useAuth() {
    return useContext(AuthContext);
}

export function AuthProvider({children}) {
    const [user, setUser] = useState(null);
    const [auth, setAuth] = useState(null);

    const isAuthenticated = async () => {
        await axios.get('/api/user/current').then((response) => {
            const data = response.data;

            setUser(data.success ? data.user : null);
            setAuth(data.success === true);
        }).catch(() => {
            setUser(null);
            setAuth(false);
        });
    };

    useEffect(() => {
        isAuthenticated().then(r => {});
    }, [auth]);

    return (
        <AuthContext.Provider value={{auth, setAuth, user}}>
            {children}
        </AuthContext.Provider>
    );
}

export function hasRole(user, role) {
    return user.roles.includes(role);
}