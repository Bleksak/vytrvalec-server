import moment from "moment/moment";
import React, { useEffect, useRef } from "react"
import { useState } from "react";
import { useTranslation } from "react-i18next";

export default function SeasonManagement() {

    const [seasons, setSeasons] = useState([]);
    const [currentSeason, setCurrentSeason] = useState(getNewSeason());
    
    const beginDate = useRef(null);
    const charityName = useRef(null);
    const charityDescription = useRef(null);

    useEffect(() => {
        fetchSeasons().then((seasons) => {
            setSeasons(seasons);
        })
    }, []);

    const newSeasonSubmit = (event) => {
        event.preventDefault();
        
        const date = beginDate.current.value;
        const name = charityName.current.value;
        const description = charityDescription.current.value;

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'beginDate='+date+"&charityName="+name+"&charityDescription="+description
        };

        fetchData(requestOptions).then((data) => {
            if(data == null || data.success === 0) {
                console.log("err");
            } else {
                const newSeason = {
                    'id': data.id,
                    'start': new Date(date),
                    'charity': {
                        'name': name,
                        'description': description
                    }
                };

                beginDate.current.value = null;
                charityName.current.value = null;
                charityDescription.current.value = null;

                setSeasons([...seasons, newSeason]);
            }
        })
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
                <li className="seasonListItem" onClick={ () => setCurrentSeason(getNewSeason()) }>
                    +
                </li>
            {
                seasons.map((season) => (
                    <li className="seasonListItem" key={season.id} onClick={ () => setCurrentSeason(season) }>
                        {moment(season.start).format('d-M-Y')}
                    </li>
                ))
            }
        </ul>
    );
    // <table className="table">
    //     <thead>
    //         <tr>
    //             <th className="py-0">{t('charity_name')}</th>
    //             <th className="py-0">{t('charity_description')}</th>
    //             <th className="py-0 text-nowrap">{t('begin_date')}</th>
    //             <th className="py-0">{t('action')}</th>
    //         </tr>
    //     </thead>
    //     <tbody>
    //         { seasons.reverse().map((season) => 
    //         <tr key={season.id}>
    //             <td>
    //                 {season.charity.name}
    //             </td>
    //             <td>
    //                 {season.charity.description}
    //             </td>
    //             <td className="text-nowrap">
    //                 { moment(season.start, 'YYYY-MM-DD').format('D. M. YYYY') }
    //             </td>
    //             <td>
    //                 { seasonRunning(season.start) 
    //                     ? <a className="text-nowrap" href={`/management/season/${season.id}`}>{t('season_running')}</a>
    //                     : <a className="text-nowrap" href={`/management/season/${season.id}`}>{t('season_not_running')}</a>
    //                 }
    //             </td>
    //         </tr>
    //         )}
    //     </tbody>
    // </table>
    // </>
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

const seasonRunning = (startDate) => {
    const start = moment.utc(startDate).utcOffset(0);
    const now = moment().utcOffset(0);
    const end = start.clone().add(4, 'weeks');

    return now.isBetween(start, end, undefined, '[)');


}

const SeasonEditor = ({currentSeason}) => {
    
    const today = moment().format('Y-MM-DD');
    
    
    const form = useRef();
    const beginDate = useRef();
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
        const url = form.current.action;
        console.log(url);
    };
    
    const editUrl = '/api/management/season/edit';
    const newUrl = '/api/management/season/new';
    
    return <>
        <form ref={form} className="form-group" method="POST" action={ currentSeason.id == null ? newUrl : editUrl } onSubmit={formSubmit}>
            <label htmlFor="beginDate">{t('begin_date')}:</label>
            <input ref={beginDate} id="beginDate" className="form-control mb-0" type="date" min={today} name="beginDate"/>

            <label htmlFor="charityName">{t('charity_name')}:</label>
            <input ref={charityName} id="charityName" className="form-control mb-0" type="text" name="charityName"/>

            <label htmlFor="charityDescription">{t('charity_description')}:</label>
            <textarea ref={charityDescription} id="charityDescription" className="form-control mb-0" name="charityDescription"></textarea>

            <div className="d-flex justify-content-center">
                <button className="btn btn-primary mt-2" type="submit">{t('create_new_season')}</button>
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
