import moment from "moment/moment";
import React, { useEffect, useRef } from "react"
import { useState } from "react";
import { useTranslation } from "react-i18next";

export default function SeasonManagement() {

    const [seasons, setSeasons] = useState([]);
    const [currentSeason, setCurrentSeason] = useState(null);

    const beginDate = useRef(null);
    const charityName = useRef(null);
    const charityDescription = useRef(null);

    useEffect(() => {
        const fetchSeasons = async () => {
            const response = await fetch('/api/seasons')
            if(response == null) return []
            return await response.json()
        }

        fetchSeasons().then((seasons) => {
            setSeasons(seasons)
        })
    }, [])

    const newSeasonSubmit = (event) => {
        event.preventDefault()
        
        const date = beginDate.current.value
        const name = charityName.current.value
        const description = charityDescription.current.value

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'beginDate='+date+"&charityName="+name+"&charityDescription="+description
        }

        const fetchData = async () => {
            const response = await fetch('/api/management/season/new', requestOptions)
            if(!response.ok) {
                return null
            }

            return await response.json()
        }

        fetchData().then((data) => {
            if(data == null || data.success == 0) {
                console.log("err")
            } else {
                seasons.push({
                    'start': new Date(date),
                    'charity': {
                        'name': name,
                        'description': description
                    }
                })


                beginDate.current.value = null
                charityName.current.value = null
                charityDescription.current.value = null

                setSeasons(seasons)
            }
        })
    }

    return <div className="py-5">
        <div className="d-flex">
            <div className="w-50">
                <CreateNewSeason formSubmit={newSeasonSubmit} beginDate={beginDate} charityName={charityName} charityDescription={charityDescription}></CreateNewSeason>
            </div>
            <div className="w-50 px-3">
                <SeasonList seasons={seasons}></SeasonList>
            </div>
        </div>
    </div>
}

function SeasonList({seasons}) {

    const [t, _] = useTranslation()
    console.log(seasons.length)

    return <>
    <table className="table">
        <thead>
            <tr>
                <th>{t('charity_name')}</th>
                <th>{t('charity_description')}</th>
                <th>{t('begin_date')}</th>
            </tr>
        </thead>
        <tbody>
            { seasons.map((season) => 
            <tr key={season.id}>
                <td>
                    {season.charity.name}
                </td>
                <td>
                    {season.charity.description}
                </td>
                <td>
                    { moment(season.charity.start).format('d. M. Y') }
                </td>
            </tr>
            )}
        </tbody>
    </table>
    </>
}

function CreateNewSeason({formSubmit, beginDate, charityName, charityDescription}) {

    const today = new Date().toJSON().split('T')[0]
    const [t, _] = useTranslation()

    return <>
        <form className="form-group" onSubmit={formSubmit}>
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
    </>
}
