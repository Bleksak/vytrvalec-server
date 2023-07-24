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
    const [stats, setStats] = useState<{ bikeKm: number, walkKm: number, elevation: number } | null>({ bikeKm: 26, walkKm: 17, elevation: 34 });
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
                                        <strong>Kolo a koloběžka</strong>
                                        <span>{stats.bikeKm} km</span>
                                    </p>

                                    <p className="margin-sm centered">
                                        <strong>Běh a chůze</strong>
                                        <span>{stats.walkKm} km</span>
                                    </p>

                                    <p className="margin-sm centered">
                                        <strong>Nastoupáno celkem</strong>
                                        <span>{stats.elevation} m</span>
                                    </p>
                                </>}

                            </div>
                            <Button className="pwd-chagnge-btn">Změnit heslo</Button>
                        </>
                    }

                    <div className="box-shadow centered user-info-box">
                        <p>Změna hesla vám umožní přístup k portálu i po ztrátě přístupu k účtu GApps. Pokud se chcete výzvy zúčastnit jako absolvent, musíte si heslo změnit. Změna hesla vám umožní přihlásit se pomocí formuláře. O možnost přihlásit se prostřednictvím aplikace GApps nepřijdete.</p>
                    </div>
                </div>

            </Col>


            <Col className="col-lg bg-blue" >
                <div className="container-new centered">
                    <u><strong>Nahraný záznam může být smazán dokud je v procesu schvalování.</strong></u>
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
