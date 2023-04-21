import React from "react";
import { useEffect } from "react";
import { useState } from "react";
import { useTranslation } from "react-i18next";

export default function SubmissionUpload() {
  
  const [t, _] = useTranslation();
  const [categories, setCategories] = useState([]);
  
  useEffect(() => {
    fetchCategories().then((data) => {
      setCategories(data);
    })
  }, []);
  
  return (
    <>
      <form action="/api/submission/upload" className="form-group" method="POST" enctype="multipart/form-data">
        
        <label htmlFor="activity">{t('activity')}</label>
        <select id="activity" name="activity" className="form-select mb-1">
          {categories.map((category) => (
            <option key={category.id} value={category.id}>{category.name}</option>
          ))}
        </select>
        
        <label htmlFor="distance">{t('distance')}</label>
        <input name="distance" id="distance" type="number" className="form-control mb-1"/>
        
        <label htmlFor="elevation">{t('elevation')}</label>
        <input name="elevation" id="elevation" type="number" className="form-control mb-1"/>
        
        <label htmlFor="screenshot">{t('screenshot')}</label>
        <input name="screenshot" id="screenshot" type="file" accept="image/*" className="form-control mb-1"/>
        
        <button className="btn btn-primary" type="submit">{t('submit')}</button>
      </form>
    </>
  );
}

const fetchCategories = async () => {
  const response = await fetch('/api/activities').catch(() => null);
  if(response == null) {
    return null;
  }
  
  return await response.json().catch(() => null);
}