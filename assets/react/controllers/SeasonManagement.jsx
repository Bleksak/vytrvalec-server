import React from "react"
import { useState } from "react";

export default function SeasonManagement() {

    const [seasons, setSeasons] = useState(null);
    const [currentSeason, setCurrentSeason] = useState(null);

    const newSeasonSubmit = (event) => {
        event.preventDefault();
    }

    return <>
    <div className="d-flex flex-wrap">
        <div className="flex-grow-1">
            <CreateNewSeason formSubmit={newSeasonSubmit}></CreateNewSeason>
        </div>
        <div className="flex-grow-1">
            <SeasonList seasons={seasons}></SeasonList>
        </div>
    </div>
    </>
}

function SeasonList({seasons}) {
    return <>
        <div>
            hi
        </div>
    </>
}

function CreateNewSeason({formSubmit}) {
    return <>
        <form className="form-group" onSubmit={formSubmit}>
            <label htmlFor="beginDate">Begin:</label>
            <input id="beginDate" className="form-control mb-0" type="date" name="beginDate"/>

            <label htmlFor="charityName">Charity name:</label>
            <input id="charityName" className="form-control mb-0" type="text" name="charityName"/>

            <label htmlFor="charityDescription">Charity description:</label>
            <textarea id="charityDescription" className="form-control mb-0" name="charityDescription"></textarea>

            <button className="btn btn-primary" type="submit">Create new season</button>
        </form>
    </>
}