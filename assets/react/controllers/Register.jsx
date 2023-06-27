import React, {useEffect, useRef, useState} from "react";
import useAuth from "./useAuth";
import {useNavigate} from "react-router-dom";
import {useTranslation} from "react-i18next";
import axios from "axios";


export default function Registration() {
    const navigate = useNavigate();
    const { auth } = useAuth();
    const {t} = useTranslation();
    const [faculties, setFaculties] = useState(null);
    const [, setTooltip] = useState(null);

    let gdpr = useRef(null);

    useEffect(() => {
            if(auth) {
                return navigate("/");
            }
        }, [auth]
    );

    useEffect(() => {
        fetchFaculties().then((data) => {
            setFaculties(data);
        });
        // TODO: CATCH?
    }, []);

    useEffect(() => {
        const bootstrap = require('bootstrap');
        if(gdpr !== undefined) {
            setTooltip(new bootstrap.Tooltip(gdpr.current, {animation: true}));
        } else {
            setTooltip(null);
        }
    }, [gdpr]);

    const submit = (ev) => {

    }

    return <>
        <div className="register">
            <h2 className="form-header text-center">{ t('sign_up') }</h2>

            <form className="black-form" method="POST" onSubmit={submit}>
                {/* TODO: TADY ERRORY*/}

                <label htmlFor="email">{ t('email') }</label>
                <input type="email" name="email" id="email" className="form-control"/>
                {/*{{form_errors(form.first_name)}}*/}

                <div className="d-flex gap-3">
                    <div>
                        {/* TODO: Error ke kazdemu fieldu zvlast */}
                        <label htmlFor="first_name">{ t('first_name') }</label>
                        <input type="text" name="first_name" id="first_name" className="form-control d-inline"/>
                        {/*{{form_errors(form.first_name)}}*/}
                    </div>

                    <div>
                        <label htmlFor="last_name">{ t('last_name') }</label>
                        <input type="text" name="last_name" id="last_name" className="form-control d-inline"/>
                        {/*{{form_errors(form.last_name)}}*/}
                    </div>
                </div>

                <label htmlFor="password">{ t('password') }</label>
                <input type="password" name="password" id="password" className="form-control"/>
                {/*{{form_errors(form.last_name)}}*/}

                <label htmlFor="password_repeat">{ t('password_repeat') }</label>
                <input type="password" name="password_repeat" id="password_repeat" className="form-control"/>

                <select className="form-control">
                    { faculties && faculties.map( (faculty) =>
                        <option key={faculty.id} value={faculty.id}>{ faculty.name }</option>
                    )}
                </select>

                <label htmlFor="gdpr">{ t('gdpr_label') }</label>
                <input ref={gdpr} type="checkbox" name="gdpr" id="gdpr" className="form-check-input border border-2 border-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title={ t('gdpr_tooltip') }/>

                <div className="d-flex justify-content-center">
                    <button type="submit" className="btn btn-primary d-block mx-1 my-1">{ t('sign_up') }</button>
                </div>

            </form>
        </div>
    </>
}

async function fetchFaculties() {
    return (await axios.get('/api/faculties/list')).data;
}
