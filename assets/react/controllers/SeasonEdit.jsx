// import React from 'react';
import { useTranslation } from 'react-i18next';
import _ from '../i8n'
import React, { useCallback, useEffect, useRef, useState } from "react";
import { useParams } from 'react-router-dom';

export default function SeasonEdit() {
    const { seasonId } = useParams();
    console.log(seasonId);

    const charityName = useRef(null);
    const charityDescription = useRef(null);

    const [season, setSeason] = useState(null);

    const [t, _] = useTranslation();

    const editCharitySubmit = () => {

    }

    useEffect(() => {
        fetchData(seasonId).then((response) => {
            setSeason(response);
            charityName.current.value = response.charity.name;
            charityDescription.current.value = response.charity.description;
        });
    }, []);

    return (
    <>
        <form className='form-group'>
            <label htmlFor="charityName">{t('charity_name')}</label>
            <input className='form-control mb-0' ref={charityName} id="charityName" name="charityName" type="text"/>

            <label htmlFor="charityDescription">{t('charity_description')}</label>
            <textarea className='form-control mb-0' ref={charityDescription} id="charityDescription" name="charityDescription"></textarea>

            <button type="button">{t('edit')}</button>
        </form>
    </>
    )
}

const fetchData = async (id) => {
    const requestOptions = {
        method: 'GET',
        // headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        // body: 'user_id='+user.id
    };
    const response = await fetch('/api/season/'+id, requestOptions).catch(() => null);
    if(response == null) {
        return null;
    }

    return await response.json().catch(() => null);
}
