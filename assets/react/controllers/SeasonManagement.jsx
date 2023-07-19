import moment from "moment/moment";
import React, {useEffect, useRef} from "react"
import {useState} from "react";
import {useTranslation} from "react-i18next";
import axios from "axios";

export default function SeasonManagement() {

    //<div className="seasonManagement">
    // <div className='seasonManagementLeft'>
    // <SeasonList seasons={seasons} setCurrentSeason={setCurrentSeason} setSubmissions={setSubmissions}/>
    // <SeasonEditor currentSeason={currentSeason}/>
    // </div>
    // <SubmissionList submissions={submissions}></SubmissionList>
    // </div>

    // const [submissions, setSubmissions] = useState([]);
    // const [seasons, setSeasons] = useState([]);
    // const [currentSeason, setCurrentSeason] = useState(getNewSeason());
    //
    // const beginDate = useRef(null);
    // const endDate = useRef(null);
    // const charityName = useRef(null);
    // const charityDescription = useRef(null);
    //
    // useEffect(() => {
    //     fetchSeasons().then((seasons) => {
    //         setSeasons(seasons);
    //     })
    // }, []);

    return <div className="container-season-management py-5">
        <div className="column menu-column">
            <ul className="menu-list">
                <li>Menu Item 1</li>
                <li>Menu Item 2</li>
                <li>Menu Item 3</li>
                <li>Menu Item 4</li>
            </ul>
        </div>

        <div className="column form-column">
            <form>
                <label htmlFor="begin-date">Begin Date:</label>
                <input type="text" id="begin-date" name="begin-date" placeholder="YYYY-MM-DD" required/>

                <label htmlFor="end-date-checkbox">End Date:</label>
                <input type="checkbox" id="end-date-checkbox" name="end-date-checkbox"/>

                <label htmlFor="end-date">End Date:</label>
                <input type="text" id="end-date" name="end-date" placeholder="YYYY-MM-DD" disabled/>

                <label htmlFor="charity-name">Charity Name:</label>
                <input type="text" id="charity-name" name="charity-name" required/>

                <label htmlFor="charity-description">Charity Description:</label>
                <textarea id="charity-description" name="charity-description" rows="4"></textarea>

                <button type="submit">Submit</button>
            </form>
        </div>

        <div className="column tinder-column">
            <div className="profile-card">
                <div className="profile-image">
                    <img src="/uploads/1.jpg" alt="Activity Image"/>
                </div>
                <div className="profile-info">
                    <div className="profile-fields">
                        <div className="field">
                            <label htmlFor="distance">Distance:</label>
                            <span id="distance">10 km</span>
                        </div>
                        <div className="field">
                            <label htmlFor="elevation">Elevation:</label>
                            <span id="elevation">500 m</span>
                        </div>
                    </div>
                    <div className="profile-buttons">
                        <button className="btn btn-danger m-1">Reject</button>
                        <button className="btn btn-success m-1">Approve</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
}

function setSeason(season, setCurrentSeason, setSubmissions) {
    if (season.id !== null) {
        fetchSubmissions(season.id).then((response) => {
            setSubmissions(response.data);
        }).catch((error) => {
            // TODO: error
        })
    } else {
        setSubmissions([]);
    }

    setCurrentSeason(season);
}

function SeasonList({
    seasons, setCurrentSeason, setSubmissions
}) {

    const [t, _] = useTranslation();

    return (
        <ul className="seasonList">
            <li className="seasonListItem btn btn-secondary"
                onClick={() => setSeason(getNewSeason(), setCurrentSeason, setSubmissions)}>
                +
            </li>
            {
                seasons.map((season) => (
                    <li className="seasonListItem btn btn-secondary" key={season.id}
                        onClick={() => setSeason(season, setCurrentSeason, setSubmissions)}>
                        {moment(season.start).format('D-M-Y')}
                    </li>
                ))
            }
        </ul>
    )
        ;
}

const getNewSeason = () => {
    return {
        id: null,
        start: moment().format(),
        end: moment().format(),
        charity: {
            id: null,
            name: '',
            description: '',
        }
    };
}


const SeasonEditor = ({
    currentSeason
}) => {

    const today = moment().format('Y-MM-DD');
    const [endDateDisabled, setEndDateDisabled] = useState(true);

    const form = useRef();
    const beginDate = useRef();
    const endDate = useRef();
    const charityName = useRef();
    const charityDescription = useRef();

    useEffect(() => {
        beginDate.current.value = moment(currentSeason.start).format('Y-MM-DD');

        if (currentSeason.id === null) {
            endDate.current.value = null;
        } else {
            endDate.current.value = moment(currentSeason.end).format('Y-MM-DD');
        }

        charityName.current.value = currentSeason.charity.name;
        charityDescription.current.value = currentSeason.charity.description;
    }, [currentSeason]);

    const [t, _] = useTranslation();

    const formSubmit = (event) => {
        event.preventDefault();
        // TODO: form errors

        if (currentSeason.id === null) {
            createSeason(beginDate.current.value, endDateDisabled ? null : endDate.current.value, charityName.current.value, charityDescription.current.value)
                .then((response) => {
                    console.log(response);
                }).catch((error) => {
                // TODO: errors
            });

        } else {
            // edit
        }
    };

    return <div className='seasonForm'>
        <form ref={form} className="form-group" method="POST" onSubmit={formSubmit}>
            <label htmlFor="beginDate">{t('begin_date')}:</label>
            <input ref={beginDate} id="beginDate" className="form-control mb-0" type="date" min={today}
                   name="beginDate"/>

            <label htmlFor="endDateCheckBox">{t('end_date')}:</label>
            <input id="endDateCheckBox" className="form-check-input mb-0 mx-2" type="checkbox"
                   name="endDateCheckBox" value={endDateDisabled ? 'on' : ''}
                   onChange={(e) => setEndDateDisabled(e.target.value === 'on')}/>
            <input ref={endDate} id="endDate" className="form-control mb-0" type="date" min={today} name="endDate"
                   disabled={endDateDisabled}/>

            <label htmlFor="charityName">{t('charity_name')}:</label>
            <input ref={charityName} id="charityName" className="form-control mb-0" type="text" name="charityName"/>

            <label htmlFor="charityDescription">{t('charity_description')}:</label>
            <textarea ref={charityDescription} id="charityDescription" className="form-control mb-0"
                      name="charityDescription"></textarea>

            <div className="d-flex justify-content-center">
                <button className="btn btn-primary mt-2"
                        type="submit">{currentSeason.id === null ? t('create_new_season') : t('edit_season')}</button>
            </div>
        </form>
    </div>;
}

function SubmissionList({
    submissions
}) {
    return <div className='submissionList'>
        {submissions.length > 0 &&
            <>
                <div className="profile-card">
                    <div className="profile-image">
                        <img src={submissions[0].image} alt="Activity Image"/>
                    </div>
                    <div className="profile-info">
                        <div className="profile-fields">
                            <div className="field">
                                <label htmlFor="distance">Distance:</label>
                                <span id="distance">{submissions[0].distance / 1000.0} km</span>
                            </div>
                            <div className="field">
                                <label htmlFor="elevation">Elevation:</label>
                                <span id="elevation">{submissions[0].elevation} m</span>
                            </div>
                        </div>
                        <div className="profile-buttons">
                            <button className="reject-button">Reject</button>
                            <button className="approve-button">Approve</button>
                        </div>
                    </div>
                </div>

            </>
        }
    </div>
}

const fetchSeasons = async () => {
    const response = await fetch('/api/season/list').catch(() => null);
    if (response == null) return [];
    return await response.json();
}

const fetchData = async (requestOptions) => {
    const response = await fetch('/api/management/season/new', requestOptions).catch(() => null);
    if (response == null) {
        return null;
    }

    return await response.json();
}

const editSeason = async (original, edited) => {
    let data = {};

    if (original.start !== edited.start) {
        data.start = edited.start;
    }

    if (original.end !== edited.end) {
        data.end = edited.end;
    }

    if (original.charity.name !== edited.charityName) {
        data.charityName = edited.charityName;
    }

    if (original.charity.description !== edited.charityDescription) {
        data.charityDescription = edited.charityDescription;
    }

    // axios.patch('/api/')
}

const createSeason = async (start, end, charityName, charityDescription) => {
    let data = {
        start: start,
        charityName: charityName,
        charityDescription: charityDescription
    };

    if (end !== null) {
        data.end = end;
    }

    return await axios.post('/api/season/create', data);
}

const fetchSubmissions = async (season, unresolved = true, page = 1) => {
    if(unresolved) {
        return await axios.get('/api/submission/unresolved/' + season);
    } else {
        return await axios.get('/api/submission/list/' + season + '/' + page);
    }
}

const acceptSubmission = async(submission) => {

}