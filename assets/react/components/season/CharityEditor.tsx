import { useTranslation } from "react-i18next";
import React from 'react';
import { editCharity } from "../../api/SeasonApi";

const CharityEditor = ({ charity, charityName, charityDescription }: any) => {
    const [t, _] = useTranslation();

    const editCharitySubmit = (ev: any) => {
        ev.preventDefault();

        const newCharity = {
            id: charity.id,
            name: charityName.current.value,
            description: charityDescription.current.value,
        };

        editCharity(newCharity).then((response) => {
            if (response == null || !response.success) {
                // TODO: show retarded msg
            } else {
                // TODO: show saved msg
                console.log("ok");
            }
        });
    }

    return (
        <form className='form-group' onSubmit={editCharitySubmit}>
            <label htmlFor="charityName">{t('charity_name')}</label>
            <input className='form-control mb-0' ref={charityName} id="charityName" name="charityName" type="text" />

            <label htmlFor="charityDescription">{t('charity_description')}</label>
            <textarea className='form-control mb-0' ref={charityDescription} id="charityDescription" name="charityDescription"></textarea>

            <button type="submit">{t('edit')}</button>
        </form>
    )
}
export default CharityEditor;