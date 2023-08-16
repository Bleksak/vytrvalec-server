import moment from "moment/moment";
import React, { useEffect, useRef } from "react"
import { useState } from "react";
import { useTranslation } from "react-i18next";
import { getNewSeason } from "../utils";
import Season from "../types/Season";
import SeasonEditor from "../components/season/SeasonEditor";
import { createNewSeason, getAllSeasons } from "../api/SeasonApi";
import SeasonList from "../components/season/SeasonList";

const SeasonManagement = () => {
    const [seasons, setSeasons] = useState<Season[]>([]);
    const [currentSeason, setCurrentSeason] = useState<Season>(getNewSeason());

    const beginDate: any = useRef(null);
    const charityName: any = useRef(null);
    const charityDescription: any = useRef(null);

    useEffect(() => {
        getAllSeasons().then(setSeasons)
    }, []);

    const newSeasonSubmit = (event: { preventDefault: () => void; }) => {
        event.preventDefault();

        const date = beginDate.current.value;
        const name = charityName.current.value;
        const description = charityDescription.current.value;

        createNewSeason(date, name, description).then((data) => {
            if (data == null || data.success === 0) {
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
            <SeasonList seasons={seasons} setCurrentSeason={setCurrentSeason} />
            <div className="seasonForm">
                <SeasonEditor currentSeason={currentSeason} />
            </div>
        </div>
    </div>
}

export default SeasonManagement;






