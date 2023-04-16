import { useTranslation } from 'react-i18next';
import _ from '../i8n'
import React, { forwardRef, useEffect, useRef, useState } from "react";
import { useParams } from 'react-router-dom';

export default function SeasonEdit() {
    const { seasonId } = useParams();

    const charityName = useRef(null);
    const charityDescription = useRef(null);

    const [season, setSeason] = useState(null);

    useEffect(() => {
        fetchData(seasonId).then((response) => {
            setSeason(response);
            charityName.current = response.charity.name;
            charityDescription.current = response.charity.description;
        });
    }, []);
    
    if(season != null) {
        return (
            <>
                <CharityEditor charity={season.charity} charityName={charityName} charityDescription={charityDescription}/>
            </>
        );
    }
}

const CharityEditor = ({charity, charityName, charityDescription}) => {
    const [t, _] = useTranslation();
    
    const editCharitySubmit = (ev) => {
        ev.preventDefault();

        const newCharity = {
            id: charity.id,
            name: charityName.current.value,
            description: charityDescription.current.value,
        };

        editCharityFetch(newCharity).then((response) => {
            if(response == null || !response.success) {
                // TODO: show retarded msg
            } else {
                // TODO: show saved msg
                console.log("ok");
            }
        });
    }

    return (
    <>
        <form className='form-group' onSubmit={editCharitySubmit}>
            <label htmlFor="charityName">{t('charity_name')}</label>
            <input className='form-control mb-0' ref={charityName} id="charityName" name="charityName" type="text"/>

            <label htmlFor="charityDescription">{t('charity_description')}</label>
            <textarea className='form-control mb-0' ref={charityDescription} id="charityDescription" name="charityDescription"></textarea>

            <button type="submit">{t('edit')}</button>
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

const editCharityFetch = async (charity) => {
    const requestOptions = {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id='+encodeURIComponent(charity.id)+'&charityName='+encodeURIComponent(charity.name)+'&charityDescription='+encodeURIComponent(charity.description)
    };
    
    const response = await fetch('/api/management/charity/edit', requestOptions).catch(() => null);
    return response == null ? null : await response.json();
}