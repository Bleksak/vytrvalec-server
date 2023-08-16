import React, { useEffect, useState } from "react";
import { getUserSubmissions } from "../api/UserApi";
import { Image, Col, Row, Button, } from 'react-bootstrap';
import Submission from "../types/Submission";
import useAuth from "../useAuth";
import SubmissionModal from "../components/user/SubmissionModal";
import { useTranslation } from "react-i18next";

const Profile = (): JSX.Element => {
    const [submissions, setSubmissions] = useState<Submission[] | null>(null);
    const [selectedSubmission, setSelectedSubmission] = useState<Submission | null>(null);
    const [stats, setStats] = useState<{ bikeKm: number, walkKm: number, elevation: number } | null>({ bikeKm: 26, walkKm: 17, elevation: 34 }); //TODO
    const { user } = useAuth();
    const [t, _] = useTranslation();

    useEffect(() => {
        getUserSubmissions(1).then(setSubmissions);
    }, []);

    return (
        <Row>
            <Col className="col-lg-4">
                <div className="centered padding-user-panel">
                    {user &&
                        <>
                            <div className="box-shadow centered user-info-box">
                                {/* @ts-ignore */}
                                <p>{user.firstName} {user.lastName}</p>
                                {/* @ts-ignore */}
                                <p>{user.faculty.name}</p>
                                {/* @ts-ignore */}
                                <p>{user.email} </p>

                                {stats && <>
                                    <p className="margin-sm centered">
                                        <strong>{t('bike_and_scooter')}</strong>
                                        <span>{stats.bikeKm} km</span>
                                    </p>

                                    <p className="margin-sm centered">
                                        <strong>{t('run_and_walk')}</strong>
                                        <span>{stats.walkKm} km</span>
                                    </p>

                                    <p className="margin-sm centered">
                                        <strong>{t('elev_total')}</strong>
                                        <span>{stats.elevation} m</span>
                                    </p>
                                </>}
                            </div>
                            <Button className="pwd-change-btn">{t('pwd_change_btn')}</Button>
                        </>
                    }

                    <div className="box-shadow centered user-info-box">
                        <p>{t('pwd_change_label')}</p>
                    </div>
                </div>
            </Col>

            <Col className="col-lg bg-blue" >
                <div className="container-new centered">
                    <u><strong>{t('delete_sub_info')}</strong></u>
                </div>

                <Row style={{ justifyContent: 'center' }}>
                    {submissions?.map((sub: Submission) => (
                        <Image key={sub.id} src={sub.image} className="img" rounded onClick={() => setSelectedSubmission(sub)} />
                    ))}
                </Row>
            </Col >

            {selectedSubmission &&
                <SubmissionModal submission={selectedSubmission} onClose={() => setSelectedSubmission(null)} />
            }
        </Row >
    )
}

export default Profile;
