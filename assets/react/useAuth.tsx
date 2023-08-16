import React, { createContext, useContext, useEffect } from "react";
import { useState } from "react";
import { User } from "./types";
import { isAuthenticated } from "./api/UserApi";

const AuthContext = createContext({
    auth: false,
    setAuth: (auth: boolean) => { },
    user: null,

});

export default function useAuth() {
    return useContext(AuthContext);
}

export function AuthProvider({ children }: any) {
    const [user, setUser] = useState<User | null>(null);
    const [auth, setAuth] = useState<boolean>(false);

    useEffect(() => {
        isAuthenticated().then((data) => {
            if (data) {
                setUser(data);
                setAuth(true);
            } else {
                setUser(null);
                setAuth(false);
            }
        }).catch(() => {
            setUser(null);
            setAuth(false);
        });
    }, [auth]);

    return (
        // @ts-ignore
        <AuthContext.Provider value={{ auth, setAuth, user }}>
            {children}
        </AuthContext.Provider>
    );
}

export function hasRole(user: User, role: string) {
    return user.roles.includes(role);
}