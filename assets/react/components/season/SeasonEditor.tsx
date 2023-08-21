import moment from "moment";
import Season from "../../types/Season";
import React, { useEffect, useRef } from 'react'
import { useTranslation } from "react-i18next";

const SeasonEditor = ({ currentSeason }: { currentSeason: Season }) => {
    const [t, _] = useTranslation();

    const today = moment().format('Y-MM-DD');
    const form: any = useRef();
    const beginDate: any = useRef();
    const charityName: any = useRef();
    const charityDescription: any = useRef();

    useEffect(() => {
        beginDate.current.value = moment(currentSeason.start).format('Y-MM-DD');
        charityName.current.value = currentSeason.charity.name;
        charityDescription.current.value = currentSeason.charity.description;
    }, [currentSeason]);


    const formSubmit = (event: { preventDefault: () => void; }) => {
        event.preventDefault();
        const url = form.current.action;
        console.log(url);
    };

    const editUrl = '/api/management/season/edit';
    const newUrl = '/api/management/season/new';

    return (
        <form ref={form} className="form-group" method="POST" action={currentSeason.id == null ? newUrl : editUrl} onSubmit={formSubmit}>
            <label htmlFor="beginDate">{t('begin_date')}:</label>
            <input ref={beginDate} id="beginDate" className="form-control mb-0" type="date" min={today} name="beginDate" />

            <label htmlFor="charityName">{t('charity_name')}:</label>
            <input ref={charityName} id="charityName" className="form-control mb-0" type="text" name="charityName" />

            <label htmlFor="charityDescription">{t('charity_description')}:</label>
            <textarea ref={charityDescription} id="charityDescription" className="form-control mb-0" name="charityDescription"></textarea>

            <div className="d-flex justify-content-center">
                <button className="btn btn-primary mt-2" type="submit">{t('create_new_season')}</button>
            </div>
        </form>
    );
}

export default SeasonEditor;
