import React, { useState, useEffect } from 'react';

export default function PriceDisplay({ skillData }) {
    const [priceTiming, setPriceTiming] = useState(null);
    const [priceContent, setPriceContent] = useState(null); // Stores conditional HTML

    useEffect(() => {
        const determineContent = () => {
            let content;
            if (skillData.show_price === 'both') {
                const perDayPrice = Math.round(skillData.price_per_day) === skillData.price_per_day ? skillData.price_per_day : Math.round(skillData.price_per_day);
                const perHourPrice = Math.round(skillData.price_per_hour) === skillData.price_per_hour ? skillData.price_per_hour : Math.round(skillData.price_per_hour);
                const pricing = `${perDayPrice} - ${perHourPrice}`;
                const timing = `Dag - Timme`;
                content = (
                    <>
                        {pricing}<sub>kr / {timing}</sub>
                    </>
                );
            } else if (skillData.show_price === 'hour') {
                const perHourPrice = Math.round(skillData.price_per_hour) === skillData.price_per_hour ? skillData.price_per_hour : Math.round(skillData.price_per_hour);
                const pricing = `${perHourPrice}`;
                const timing = `Timme`;
                content = (
                    <>
                        {pricing}<sub>kr / {timing}</sub>
                    </>
                );
            } else if (skillData.show_price === 'day') {
                const perDayPrice = Math.round(skillData.price_per_day) === skillData.price_per_day ? skillData.price_per_day : Math.round(skillData.price_per_day);
                const pricing = `${perDayPrice}`;
                const timing = `Dag`;
                content = (
                    <>
                        {pricing}<sub>kr / {timing}</sub>
                    </>
                );
            }
            setPriceContent(content);
        };

        determineContent();
    }, [skillData]); // Dependency array ensures effect runs on skillData change

    return (
        <>
            {priceContent}
        </>
    );
}