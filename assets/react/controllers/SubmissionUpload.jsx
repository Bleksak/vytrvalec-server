import React, {useRef} from "react";
import {useEffect} from "react";
import {useState} from "react";
import {useTranslation} from "react-i18next";
import axios from "axios";

export default function SubmissionUpload() {

    const [t, _] = useTranslation();
    const [activities, setActivities] = useState([]);

    const activityRef = useRef(null);
    const distanceRef = useRef(null);
    const elevationRef = useRef(null);
    const imageRef = useRef(null);

    useEffect(() => {
        fetchActivities().then((response) => {
            setActivities(response.data);
        }).catch((err) => {
            // TODO: error
            console.log(err);
        })
    }, []);

    const upload = (ev) => {
        ev.preventDefault();

        if(activityRef.current.value <= 0 || activityRef.current.value >= activities.length) {
            // TODO: invalid activity
        }

        if(distanceRef.current.value <= 0) {
            // TODO: negative distance
        }

        if(!Number.isInteger(distanceRef.current.value)) {
            // TODO: non int distance
        }

        if(elevationRef.current.value < 0) {
            // TODO: negative elevation
            console.log("asdf")
        }

        if(!Number.isInteger(elevationRef.current.value) && elevationRef.current.value !== "") {
            // TODO: non int elevation
        }

        let data = {};
        data.distance = distanceRef.current.value;
        data.activity = activityRef.current.value;
        data.image = imageRef.current.files[0];

        if(elevationRef.current.value != null && elevationRef.current.value !== "") {
            data.elevation = elevationRef.current.value;
        }

        uploadSubmission(data).then((res) => {
            // TODO: success
            console.log(res);
            console.log('upload ok');
        }).catch((err) => {
            console.log('asdf')
            // TODO: error
        });
    }

    return (
        <>
            <form action="/api/submission/create" className="form-group" method="POST" encType="multipart/form-data" onSubmit={upload}>

                <label htmlFor="activity">{t('activity')}</label>
                <select ref={activityRef} id="activity" name="activity" className="form-select mb-1">
                    { activities && activities.map((activity) => (
                        <option key={activity.id} value={activity.id}>{activity.name}</option>
                    ))}
                </select>

                <label htmlFor="distance">{t('distance')}</label>
                <input ref={distanceRef} name="distance" id="distance" type="number" className="form-control mb-1"/>

                <label htmlFor="elevation">{t('elevation')}</label>
                <input ref={elevationRef} name="elevation" id="elevation" type="number" className="form-control mb-1"/>

                <label htmlFor="image">{t('screenshot')}</label>
                <input ref={imageRef} name="image" id="image" type="file" accept="image/*" className="form-control mb-1"/>

                <button className="btn btn-primary" type="submit">{t('submit')}</button>
            </form>
        </>
    );
}

const fetchActivities = async () => {
    return await axios.get('/api/activity/list');
}

const uploadSubmission = async(data) => {
    return await axios.postForm('/api/submission/create', data);
}