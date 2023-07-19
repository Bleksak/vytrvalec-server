import useAuth from "../useAuth";
import React, { useEffect, useRef } from "react";
import { useNavigate } from "react-router-dom";
import { useTranslation } from "react-i18next";
import { login } from "../api/UserApi";

const Login = () => {
    const navigate = useNavigate();
    const { auth, setAuth } = useAuth();
    const [t, _] = useTranslation();

    const usernameRef: any = useRef();
    const passwordRef: any = useRef();

    useEffect(() => {
        if (auth === true) {
            return navigate("/");
        }
    }, [auth]
    );

    useEffect(() => { // ?? Nerozumim
        if (auth) {
            navigate('/');
        }
    });

    const formSubmit = (ev: { preventDefault: () => void; }) => {
        ev.preventDefault();

        const username = usernameRef.current.value;
        const password = passwordRef.current.value;

        login(username, password).then(data => {
            setAuth(true);
        }).catch((err) => {
            // TODO: errors
        });
    }

    return (
        <div className="login">
            <form className="black-form" method="POST" action="/user/login" onSubmit={formSubmit}>
                <label htmlFor="username">{t('email')}</label>
                <input className="form-control" id="username" type="text" name="_username" ref={usernameRef} />
                <label htmlFor="password">{t('password')}</label>
                <input className="form-control" id="password" type="password" name="_password" ref={passwordRef} />

                <div className="d-flex justify-content-center">
                    <button className="btn btn-primary" type="submit">{t('login')}</button>
                </div>
            </form>
        </div>
    )
}

export default Login;

