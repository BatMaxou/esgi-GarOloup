import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';

const Buttons = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Buttons
      </Typography>
      <div className="flex flex-col flex-wrap gap-8">
        <ul className="bg-linear-(--primary-gradient) rounded-sm p-8 flex flex-wrap gap-6 shadow-(--shadow) w-fit">
          <li>
            <Button variant="gradient" label="Button" />
          </li>
          <li>
            <Button variant="neutral" label="Button" />
          </li>
          <li>
            <Button variant="neutral" glass label="Button" />
          </li>
          <li>
            <Button variant="primary" label="Button" />
          </li>
          <li>
            <Button variant="primary" glass label="Button" />
          </li>
          <li>
            <Button variant="secondary" label="Button" />
          </li>
          <li>
            <Button variant="secondary" glass label="Button" />
          </li>
        </ul>
      </div>
    </>
  );
};

export default Buttons;
