import React from 'react';
import Season from '../../types/Season';
import { getNewSeason } from '../../utils';
import { useTranslation } from 'react-i18next';
import moment from 'moment';

const SeasonList = ({ seasons, setCurrentSeason }: { seasons: Season[], setCurrentSeason: (season: Season) => void }) => {
    const [t, _] = useTranslation();

    const seasonRunning = (startDate: Date) => {
        const start = moment.utc(startDate).utcOffset(0);
        const now = moment().utcOffset(0);
        const end = start.clone().add(4, 'weeks');

        return now.isBetween(start, end, undefined, '[)');
    }

    return (
        <ul className="seasonList">
            <li className="seasonListItem" onClick={() => setCurrentSeason(getNewSeason())}>
                +
            </li>
            {
                seasons.map((season) => (
                    <li className="seasonListItem" key={season.id} onClick={() => setCurrentSeason(season)}>
                        {moment(season.start).format('d-M-Y')}
                    </li>
                ))
            }
        </ul>
    );
    // <table className="table">
    //     <thead>
    //         <tr>
    //             <th className="py-0">{t('charity_name')}</th>
    //             <th className="py-0">{t('charity_description')}</th>
    //             <th className="py-0 text-nowrap">{t('begin_date')}</th>
    //             <th className="py-0">{t('action')}</th>
    //         </tr>
    //     </thead>
    //     <tbody>
    //         { seasons.reverse().map((season) => 
    //         <tr key={season.id}>
    //             <td>
    //                 {season.charity.name}
    //             </td>
    //             <td>
    //                 {season.charity.description}
    //             </td>
    //             <td className="text-nowrap">
    //                 { moment(season.start, 'YYYY-MM-DD').format('D. M. YYYY') }
    //             </td>
    //             <td>
    //                 { seasonRunning(season.start) 
    //                     ? <a className="text-nowrap" href={`/management/season/${season.id}`}>{t('season_running')}</a>
    //                     : <a className="text-nowrap" href={`/management/season/${season.id}`}>{t('season_not_running')}</a>
    //                 }
    //             </td>
    //         </tr>
    //         )}
    //     </tbody>
    // </table>
    // </>
}

export default SeasonList;
