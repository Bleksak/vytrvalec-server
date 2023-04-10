import React from 'react'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import _ from '../i8n'
import UserTable from './UserTable'
import SeasonEdit from './SeasonEdit'

export default function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path='/management/users' element={<SeasonEdit seasonId={10} />} />
            </Routes>
        </BrowserRouter>
    )
}