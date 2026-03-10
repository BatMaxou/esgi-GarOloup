import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';

const Buttons = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Buttons
      </Typography>
      <div className="flex flex gap-8">
        <ul className="bg-background rounded-sm p-8 flex flex-wrap gap-6 shadow-(--shadow) w-fit">
          <li>
            <Button variant="primary" label="Primary" />
          </li>
          <li>
            <Button variant="primary" glass label="Primary Glass" />
          </li>
          <li>
            <Button variant="secondary" label="Secondary" />
          </li>
          <li>
            <Button variant="secondary" glass label="Secondary Glass" />
          </li>
          <li>
            <Button variant="accent" label="Accent" />
          </li>
          <li>
            <Button variant="accent" glass label="Accent Glass" />
          </li>
          <li>
            <Button variant="neutral" label="Neutral" />
          </li>
          <li>
            <Button variant="neutral" glass label="Neutral Glass" />
          </li>
          <li>
            <Button variant="error" label="Error" />
          </li>
          <li>
            <Button variant="error" glass label="Error Glass" />
          </li>
          <li>
            <Button variant="success" label="Success" />
          </li>
          <li>
            <Button variant="success" glass label="Success Glass" />
          </li>
          <li>
            <Button variant="gradient" label="Gradient" />
          </li>
          <li>
            <Button variant="text" label="Text" />
          </li>
        </ul>
      </div>
    </>
  );
};

export default Buttons;
