import classNames from 'classnames';
import * as Icon from 'react-feather';
import { Link } from 'react-router-dom';
import { useLocation } from 'react-router';
import { useFlashKey } from '@/plugins/useFlash';
import React, { useEffect, useState } from 'react';
import Spinner from '@/components/elements/Spinner';
import Tooltip from '@/components/elements/tooltip/Tooltip';
import FlashMessageRender from '@/components/FlashMessageRender';
import { styles as btnStyles } from '@/components/elements/button/index';
import PaginationFooter from '@/components/elements/table/PaginationFooter';
import { ActivityLogFilters, useActivityLogs } from '@/api/account/activity';
import ActivityLogEntry from '@/components/elements/activity/ActivityLogEntry';

export default () => {
    const location = useLocation();

    const { clearAndAddHttpError } = useFlashKey('account');
    const [ filters, setFilters ] = useState<ActivityLogFilters>({ page: 1, sorts: { timestamp: -1 } });
    const { data, isValidating, error } = useActivityLogs(filters, {
        revalidateOnMount: true,
        revalidateOnFocus: false,
    });

    useEffect(() => {
        const parsed = new URLSearchParams(location.search);

        setFilters(value => ({ ...value, filters: { ip: parsed.get('ip'), event: parsed.get('event') } }));
    }, [ location.search ]);

    useEffect(() => {
        clearAndAddHttpError(error);
    }, [ error ]);

    return (
        <>
            <FlashMessageRender byKey={'account'}/>
            {(filters.filters?.event || filters.filters?.ip) &&
                <div className={'flex justify-end mb-2'}>
                    <Link
                        to={'#'}
                        className={classNames(btnStyles.button, btnStyles.text, 'w-full sm:w-auto')}
                        onClick={() => setFilters(value => ({ ...value, filters: {} }))}
                    >
                        Clear Filters <Icon.XCircle className={'w-4 h-4 ml-2'}/>
                    </Link>
                </div>
            }
            {!data && isValidating ?
                <Spinner centered/>
                :
                <div className={'bg-gray-850'}>
                    {data?.items.map((activity) => (
                        <ActivityLogEntry key={activity.timestamp.toString() + activity.event} activity={activity}>
                            {typeof activity.properties.useragent === 'string' &&
                                <Tooltip content={activity.properties.useragent} placement={'top'}>
                                    <span><Icon.Monitor/></span>
                                </Tooltip>
                            }
                        </ActivityLogEntry>
                    ))}
                </div>
            }
            {data && <PaginationFooter
                pagination={data.pagination}
                onPageSelect={page => setFilters(value => ({ ...value, page }))}
            />}
        </>
    );
};
