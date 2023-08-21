import React, { useEffect, useRef, useState } from "react";
import { useParams } from 'react-router-dom';
import Season from '../types/Season';
import CharityEditor from '../components/season/CharityEditor';
import { getSeasonData } from '../api/SeasonApi';

const SeasonEdit = (): JSX.Element => {
    const { seasonId } = useParams();
    const charityName: any = useRef(null);
    const charityDescription: any = useRef(null);
    const [season, setSeason] = useState<Season | null>(null);

    useEffect(() => {
        getSeasonData(seasonId).then((response) => {
            setSeason(response);
            charityName.current = response.charity.name;
            charityDescription.current = response.charity.description;
        });
    }, []);

    if (season == null) {
        return <></>
    }

    return (
        <CharityEditor charity={season.charity} charityName={charityName} charityDescription={charityDescription} />
    );
}

export default SeasonEdit;



