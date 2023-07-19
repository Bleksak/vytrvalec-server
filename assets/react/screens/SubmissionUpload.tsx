import React, { useRef } from "react";
import { useEffect } from "react";
import { useState } from "react";
import { useTranslation } from "react-i18next";
import axios from "axios";
import { getAllActivities } from "../api/ActivityApi";
import Activity from "../types/Activity";
import { getRunningSeason } from "../api/SeasonApi";
import { uploadSubmission } from "../api/SubmissionApi";
import UploadSubmissionData from "../types/formDataTypes/UploadSubmissionData";

const SubmissionUpload = () => {
    const [t, _] = useTranslation();
    const [seasonRunning, setSeasonRunning] = useState<boolean>(false);
    const [activities, setActivities] = useState<Activity[]>([]);

    const activityRef: any = useRef(null);
    const distanceRef: any = useRef(null);
    const elevationRef: any = useRef(null);
    const imageRef: any = useRef(null);

    useEffect(() => {
        getRunningSeason().then(() => {
            setSeasonRunning(true);
        }).catch(() => {
            setSeasonRunning(false);
        });

    }, []);

    useEffect(() => {
        if (!seasonRunning) return;
        getAllActivities().then(setActivities);
    }, [seasonRunning])

    const upload = (ev: any) => {
        ev.preventDefault();

        if (activityRef.current.value <= 0 || activityRef.current.value >= activities.length) {
            // TODO: invalid activity
        }

        if (distanceRef.current.value <= 0) {
            // TODO: negative distance
        }

        if (!Number.isInteger(distanceRef.current.value)) {
            // TODO: non int distance
        }

        if (elevationRef.current.value < 0) {
            // TODO: negative elevation
            console.log("asdf")
        }

        if (!Number.isInteger(elevationRef.current.value) && elevationRef.current.value !== "") {
            // TODO: non int elevation
        }

        let data: UploadSubmissionData = {
            distance: distanceRef.current.value,
            activity: activityRef.current.value,
            image: imageRef.current.files[0],

        };

        if (elevationRef.current.value != null && elevationRef.current.value !== "") {
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
            <div className="submission">
                <h2 className="form-header">{t('submission_create_title')}</h2>
                <form action="/api/submission/create" className="form-group black-form" method="POST" encType="multipart/form-data"
                    onSubmit={upload}>

                    <label htmlFor="activity">{t('activity')}</label>
                    <select ref={activityRef} id="activity" name="activity" className="form-select mb-1">
                        {activities && activities.map((activity) => (
                            <option key={activity.id} value={activity.id}>{activity.name}</option>
                        ))}
                    </select>

                    <label htmlFor="distance">{t('distance')}</label>
                    <input ref={distanceRef} name="distance" id="distance" type="number" className="form-control mb-1" />

                    <label htmlFor="elevation">{t('elevation')}</label>
                    <input ref={elevationRef} name="elevation" id="elevation" type="number"
                        className="form-control mb-1" />

                    <label htmlFor="image">{t('screenshot')}</label>
                    <input ref={imageRef} name="image" id="image" type="file" accept="image/*"
                        className="form-control mb-1" />
                    <div className="d-flex justify-content-center">
                        <button className="btn btn-primary mt-2" type="submit">{t('submit')}</button>
                    </div>
                </form>
            </div>
        </>
    );
}

export default SubmissionUpload;




