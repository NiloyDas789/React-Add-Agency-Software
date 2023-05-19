import Table from '@/Components/Global/Table';
import Authenticated from '@/Layouts/Authenticated';
import CsvDownload from '@/Components/Global/CsvDownload';
import TableFooter from '@/Components/Global/TableFooter';

export default function Index({ auth, activityLogs }) {
  const { data, links, current_page: page, per_page: perPage } = activityLogs;

  return (
    <Authenticated auth={auth}>
      <CsvDownload href={route('activity_log.export', { page, perPage })}>CSV Download</CsvDownload>
      <Table>
        <thead>
          <tr className="bg-slate-200">
            <Table.Th>User Name</Table.Th>
            <Table.Th>User Email</Table.Th>
            <Table.Th>Module</Table.Th>
            <Table.Th>Description</Table.Th>
            <Table.Th>Effected Ids</Table.Th>
            <Table.Th>Event</Table.Th>
            <Table.Th>Activity Time</Table.Th>
          </tr>
        </thead>
        <tbody>
          {data.map((activityLog) => (
            <tr key={activityLog.id}>
              <Table.Td>{activityLog.causer?.name}</Table.Td>
              <Table.Td>{activityLog.causer?.email}</Table.Td>
              <Table.Td>{activityLog.log_name}</Table.Td>
              <Table.Td>{activityLog.description}</Table.Td>
              <Table.Td>
                {activityLog.subject_id ?? activityLog.properties?.ids?.map((id) => id).join(', ')}
              </Table.Td>
              <Table.Td>{activityLog.event}</Table.Td>
              <Table.Td>{activityLog.updated_at}</Table.Td>
            </tr>
          ))}
        </tbody>
      </Table>

      {data.length !== 0 && (
        <TableFooter links={links} perPage={perPage} page={page} routeName={'activity-log'} />
      )}

      {data.length === 0 && <div className="p-4 text-center">No data found.</div>}
    </Authenticated>
  );
}
