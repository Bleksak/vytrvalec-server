import moment from "moment/moment";
import React, { useEffect, useRef } from "react"
import { useState } from "react";
import { useTranslation } from "react-i18next";
import axios from "axios";

export default function SeasonManagement() {

    const [seasons, setSeasons] = useState([]);
    const [currentSeason, setCurrentSeason] = useState(getNewSeason());
    
    const beginDate = useRef(null);
    const endDate = useRef(null);
    const charityName = useRef(null);
    const charityDescription = useRef(null);

    useEffect(() => {
        fetchSeasons().then((seasons) => {
            setSeasons(seasons);
        })
    }, []);

    const newSeasonSubmit = (event) => {
        event.preventDefault();
        
        const start = beginDate.current.value;
        const end = endDate.current.value;
        const name = charityName.current.value;
        const description = charityDescription.current.value;


    }

    return <div className="py-5">
        <div className="seasonManagement">
            <SeasonList seasons={seasons} setCurrentSeason={setCurrentSeason}/>
            <div className="seasonForm">
                <SeasonEditor currentSeason={currentSeason} />
            </div>
        </div>
    </div>
}

function SeasonList({seasons, setCurrentSeason}) {

    const [t, _] = useTranslation();
    
    return (
        <ul className="seasonList">
                <li className="seasonListItem btn btn-secondary" onClick={ () => setCurrentSeason(getNewSeason()) }>
                    +
                </li>
            {
                seasons.map((season) => (
                    <li className="seasonListItem btn btn-secondary" key={season.id} onClick={ () => setCurrentSeason(season) }>
                        {moment(season.start).format('D-M-Y')}
                    </li>
                ))
            }
        </ul>
    );
}

const getNewSeason = () => {
    return {
        id: null,
        start: moment().format(),
        charity: {
            id: null,
            name: '',
            description: '',
        }
    };
}


const SeasonEditor = ({currentSeason}) => {
    
    const today = moment().format('Y-MM-DD');
    const [endDateDisabled, setEndDateDisabled]= useState(true);

    const form = useRef();
    const beginDate = useRef();
    const endDate = useRef();
    const charityName = useRef();
    const charityDescription = useRef();
    
    useEffect(() => {
        beginDate.current.value = moment(currentSeason.start).format('Y-MM-DD');
        charityName.current.value = currentSeason.charity.name;
        charityDescription.current.value = currentSeason.charity.description;
    }, [currentSeason]);
    
    const [t, _] = useTranslation();
    
    const formSubmit = (event) => {
        event.preventDefault();
        // TODO: Errors

        if(currentSeason.id === null) {
            // create

            createSeason(beginDate.current.value, endDateDisabled ? null : endDate.current.value, charityName.current.value, charityDescription.current.value)
                .then((response) => {
                    console.log(response);
                }).catch((error) => {
                });

        } else {
            // edit
        }
    };

    return <>
        <form ref={form} className="form-group" method="POST" onSubmit={formSubmit}>
            <label htmlFor="beginDate">{t('begin_date')}:</label>
            <input ref={beginDate} id="beginDate" className="form-control mb-0" type="date" min={today} name="beginDate"/>

            <label htmlFor="endDateCheckBox">{t('end_date')}:</label>
            <input id="endDateCheckBox" className="form-check-input mb-0 mx-2" type="checkbox" name="endDateCheckBox" value={endDateDisabled ? 'on' : ''} onChange={(e) => setEndDateDisabled(e.target.value === 'on')}/>
            <input ref={endDate} id="endDate" className="form-control mb-0" type="date" min={today} name="endDate" disabled={endDateDisabled} />

            <label htmlFor="charityName">{t('charity_name')}:</label>
            <input ref={charityName} id="charityName" className="form-control mb-0" type="text" name="charityName"/>

            <label htmlFor="charityDescription">{t('charity_description')}:</label>
            <textarea ref={charityDescription} id="charityDescription" className="form-control mb-0" name="charityDescription"></textarea>

            <div className="d-flex justify-content-center">
                <button className="btn btn-primary mt-2" type="submit">{ currentSeason.id === null ? t('create_new_season') : t('edit_season') }</button>
            </div>
        </form>
    </>;
    
}

const fetchSeasons = async () => {
    const response = await fetch('/api/season/list').catch(() => null);
    if(response == null) return [];
    return await response.json();
}

const fetchData = async (requestOptions) => {
    const response = await fetch('/api/management/season/new', requestOptions).catch( () => null );
    if(response == null) {
        return null;
    }

    return await response.json();
}

const editSeason = async(original, edited) => {
    let data = {};

    if(original.start !== edited.start) {
        data.start = edited.start;
    }

    if(original.end !== edited.end) {
        data.end = edited.end;
    }

    if(original.charity.name !== edited.charityName) {
        data.charityName = edited.charityName;
    }

    if(original.charity.description !== edited.charityDescription) {
        data.charityDescription = edited.charityDescription;
    }

    // axios.patch('/api/')
}

const createSeason = async(start, end, charityName, charityDescription) => {
    let data = {
        start: start,
        charityName: charityName,
        charityDescription: charityDescription
    };

    if(end !== null) {
        data.end = end;
    }

    return await axios.post('/api/season/create', data);
}